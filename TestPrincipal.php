<?php
require_once 'BaseDatos.php';
require_once 'Empresa.php';
require_once 'Viaje.php';
require_once 'Pasajero.php';
require_once 'ResponsableV.php';

// Función para mostrar el menú principal
function mostrarMenuPrincipal()
{
    echo "----- MENÚ PRINCIPAL -----\n";
    echo "1. Menú de Empresa\n";
    echo "2. Menú de Viaje\n";
    echo "3. Menú de ResponsableV\n";
    echo "4. Menú de Pasajero\n";
    echo "5. Salir\n";
    echo "Ingrese una opción: ";
}

// Función para mostrar el menú de Empresa
function mostrarMenuEmpresa()
{
    echo "----- MENÚ DE EMPRESA -----\n";
    echo "1. Crear una empresa\n";
    echo "2. Modificar una empresa\n";
    echo "3. Eliminar una empresa\n";
    echo "4. Listar todas las empresas\n";
    echo "Ingrese una opción: ";
}

// Función para mostrar el menú de Viaje
function mostrarMenuViaje()
{
    echo "----- MENÚ DE VIAJE -----\n";
    echo "1. Ingresar un viaje\n";
    echo "2. Modificar un viaje\n";
    echo "3. Eliminar un viaje\n";
    echo "4. Listar todos los viajes\n";
    echo "Ingrese una opción: ";
}

// Función para mostrar el menú de ResponsableV
function mostrarMenuResponsableV()
{
    echo "----- MENÚ DE RESPONSABLEV -----\n";
    echo "1. Ingresar un ResponsableV\n";
    echo "2. Modificar un ResponsableV\n";
    echo "3. Eliminar un ResponsableV\n";
    echo "4. Listar los ResponsablesV\n";
    echo "Ingrese una opción: ";
}

// Función para mostrar el menú de Pasajero
function mostrarMenuPasajero()
{
    echo "----- MENÚ DE PASAJERO -----\n";
    echo "1. Ingresar un Pasajero\n";
    echo "2. Modificar un Pasajero\n";
    echo "3. Eliminar un Pasajero\n";
    echo "4. Listar los Pasajeros\n";
    echo "Ingrese una opción: ";
}

// Función para crear una empresa
function crearEmpresa()
{

    echo "----- Ingresar Empresa -----\n";
    $empresa = new Empresa();
    // Solicitar datos de la empresa
    echo "Ingrese el nombre: ";
    $nombre = trim(fgets(STDIN));
    echo "Ingrese la dirección: ";
    $direccion = trim(fgets(STDIN));

    // Crear instancia de Empresa y cargar los datos

    $empresa->cargar(0, $nombre, $direccion);
    // Insertar la empresa en la base de datos
    if ($empresa->insertar()) {
        echo "Empresa ingresada correctamente.\n";
        do{
            echo "----- ¿Quiere ingresar viajes? (s/n) -----\n";
            $opcionViajes = strtolower(trim(fgets(STDIN)));
            if($opcionViajes === 's'){
                $viaje = ingresarViajeAux($empresa);
                $empresa->cargar(0, $nombre, $direccion);
            }
        }while ($opcionViajes === 's');
    } else {
        echo "Error al ingresar la empresa: " . $empresa->getmensajeoperacion() . "\n";
    }
    

    echo "Presione Enter para continuar...";
    fgets(STDIN);
}

function ingresarViajeAux($empresa)
{
    echo "----- Ingresar Viaje -----\n";
    $viaje = new Viaje();
    // Solicitar datos del viaje
    echo "Ingrese el destino: ";
    $destino = trim(fgets(STDIN));
    echo "Ingrese la cantidad máxima de pasajeros: ";
    $maxPasajeros = trim(fgets(STDIN));
    echo "Ingrese el valor: $";
    $valor = trim(fgets(STDIN));
    do{
        echo "----- Ingresar el numero de un Responsable -----\n";
        listarResponsablesV();
        $numResponsableV = trim(fgets(STDIN));
        $responsable = new ResponsableV();
        if($responsable->Buscar( $numResponsableV)){
            echo "\n Responsable: " . $numResponsableV . " quedo a cargo del viaje!!\n";

        }else{
            echo "no existe un responsable con ese numeroEmpleado\n";
            echo "----- ¿Quiere cargarlo? (s/n) -----\n";
            $opcionViajes = strtolower(trim(fgets(STDIN)));
            if($opcionViajes === 's'){
             $responsable = ingresarResponsableV();
            }
        }
    }while(!$responsable->Buscar($numResponsableV));
    
    $viaje->cargar(0, $destino, $maxPasajeros, $responsable, $valor,$empresa);
    $viaje->setEmpresa($empresa);
    // Insertar el viaje en la base de datos
    if ($viaje->insertar()) {
        echo "\nViaje ingresado correctamente.\n";
        $cantPasajero = count(Pasajero::listar("idviaje=". $viaje->getIdViaje()));
        $corte = false;
        while($maxPasajeros > $cantPasajero && !$corte){
            echo "----- ¿Quiere ingresar Pasajeros? (s/n) -----\n";
            $opcionPasajero = strtolower(trim(fgets(STDIN)));
            if($opcionPasajero === "s"){
                $nuevoPasa = ingresarPasajeroAux($viaje);
                $viaje->setPasajeros(Pasajero::listar("idviaje=".$viaje->getIdViaje()));
            }else{
                $corte= true;
            }
        }
       $empresa->setViajes(Viaje::listar("idempresa=".$empresa->getIdEmpresa()));
       
    } else {
        echo "Error al ingresar el viaje: " . $viaje->getmensajeoperacion() . "\n";
    }



    echo "Presione Enter para continuar...";
    fgets(STDIN);

    return $viaje;
}

function ingresarPasajeroAux($viaje)
{
    echo "----- Ingresar Pasajero -----\n";
    $pasajero = new Pasajero();
    // Solicitar datos del pasajero
    echo "Ingrese el documento: ";
    $documento = trim(fgets(STDIN));
    if(count($pasajero->listar("pdocumento=".$documento." AND idviaje=".$viaje->getIdViaje())) === 0){
        echo "Ingrese el nombre: ";
    $nombre = trim(fgets(STDIN));
    echo "Ingrese el apellido: ";
    $apellido = trim(fgets(STDIN));
    echo "Ingrese el teléfono: ";
    $telefono = trim(fgets(STDIN));

    // Crear instancia de Pasajero y cargar los datos
    
    
    $pasajero->cargar($documento, $nombre, $apellido, $telefono);
    $pasajero->setViaje($viaje);
    // Insertar el pasajero en la base de datos
    if ($pasajero->insertar()) {
        echo "Pasajero ingresado correctamente.\n";
    } else {
        echo "Error al ingresar el pasajero: " . $pasajero->getmensajeoperacion() . "\n";
    }
    }else{
        echo "El pasajero ya esta en el viaje";
    }
    

    echo "Presione Enter para continuar...";
    fgets(STDIN);
    return $pasajero;
}


// Función para modificar una empresa
function modificarEmpresa()
{


    echo "----- Modificar Empresa -----\n";
    listarEmpresas();
    // Solicitar ID de la empresa a modificar
    echo "\nIngrese el ID de la empresa a modificar: ";
    $idEmpresa = trim(fgets(STDIN));
    // Buscar la empresa en la base de datos
    $empresa = new Empresa();
    if ($empresa->Buscar($idEmpresa)) {
        // Solicitar nuevos datos de la empresa
        echo "Ingrese el nuevo nombre (actual: " . $empresa->getNombre() . " // 0 para saltar paso): ";
        $nombre = trim(fgets(STDIN));
        echo "Ingrese la nueva dirección (actual: " . $empresa->getDireccion() . " // 0 para saltar paso): ";
        $direccion = trim(fgets(STDIN));
        // Actualizar la información de la empresa
        if($nombre !== 0){
            $empresa->setNombre($nombre);
        }
        if($direccion !== 0){
            $empresa->setDireccion($direccion);
        }
        // Guardar los cambios en la base de datos
        if ($empresa->modificar()) {
            echo "Empresa modificada correctamente.\n";
        } else {
            echo "Error al modificar la empresa: " . $empresa->getmensajeoperacion() . "\n";
        }
    } else {
        echo "No se encontró una empresa con el ID ingresado.\n";
    }

    echo "Presione Enter para continuar...";
    fgets(STDIN);
}

// Función para eliminar una empresa
function eliminarEmpresa()
{
    echo "----- Eliminar Empresa -----\n";
    listarEmpresas();
    // Solicitar ID de la empresa a eliminar
    echo "Ingrese el ID de la empresa a eliminar: ";
    $idEmpresa = trim(fgets(STDIN));

    // Buscar la empresa en la base de datos
    $empresa = new Empresa();
    if ($empresa->Buscar($idEmpresa)) {
        // Confirmar la eliminación
        echo "Si elimina esta empresa se eliminaran los viajes contenidos y los pasajeros dentro de viaje\n";
        echo "¿Está seguro de que desea eliminar la empresa? (S/N): \n";

        $confirmacion = strtoupper(trim(fgets(STDIN)));

        if ($confirmacion === 'S') {

            $viajes = $empresa->getViajes();

            foreach ($viajes as $viaje) {
                $pasajeros = $viaje->getPasajeros();

                foreach ($pasajeros as $pasajero); {
                    $documento = $pasajero->getDocumento();
                    if ($pasajero->Buscar($documento)) {
                        // Eliminar el pasajero de la base de datos
                        if ($pasajero->eliminar()) {
                            echo "Pasajero--> ". $documento ." - eliminado correctamente.\n";
                        } else {
                            echo "Error al eliminar el pasajero: " . $pasajero->getmensajeoperacion() . "\n";
                        }
                    } else {
                        echo "No se encontró un pasajero con el documento ingresado.\n";
                    }
                }
                $idViaje = $viaje->getIdViaje();
                if ($viaje->buscar($idViaje)) {
                    if ($viaje->eliminar()) {
                        echo "Viaje-->". $idViaje ." - eliminado correctamente.\n";
                    } else {
                        echo "Error al eliminar el viaje: " . $viaje->getmensajeoperacion() . "\n";
                    }
                } else {
                    echo "No se encontró un viaje con el ID ingresado.\n";
                }
            }
            // Eliminar la empresa de la base de datos
            if ($empresa->eliminar()) {
                echo "Empresa-->" .$idEmpresa. " - eliminada correctamente.\n";
            } else {
                echo "Error al eliminar la empresa: " . $empresa->getmensajeoperacion() . "\n";
            }
        } else {
            echo "Operación cancelada.\n";
        }
    } else {
        echo "No se encontró una empresa con el ID ingresado.\n";
    }

    echo "Presione Enter para continuar...";
    fgets(STDIN);
}

// Función para listar todas las empresas
function listarEmpresas()
{
    echo "----- Listar Empresas -----\n";

    // Obtener el listado de empresas desde la base de datos
    $empresas = Empresa::listar();

    // Verificar si hay empresas para mostrar
    if (!empty($empresas)) {
        // Mostrar la información de cada empresa
        foreach ($empresas as $empresa) {
            echo $empresa->__toString();
            echo "\n";
        }
    } else {
        echo "No hay empresas registradas.\n";
    }

    echo "Presione Enter para continuar...";
    fgets(STDIN);
}


function ingresarViaje()
{

    echo "----- Ingresar el idEmpresa de la empresa a la cual pertenecera el viaje ----- \n";
    listarEmpresas();
    $idEmpresa = trim(fgets(STDIN));
    $empresa = new Empresa();
    if ($empresa->Buscar($idEmpresa)) {
        $viaje= ingresarViajeAux($empresa);
    } else {
        echo "No se encontró un viaje con el ID ingresado.\n";
    }
}

// Función para modificar un viaje
function modificarViaje()
{
    echo "----- Modificar Viaje -----\n";
    listarViajes();
    // Solicitar ID del viaje a modificar
    echo "Ingrese el ID del viaje a modificar: \n";
    $idViaje = trim(fgets(STDIN));

    // Buscar el viaje en la base de datos
    $viaje = new Viaje();
    if ($viaje->Buscar($idViaje)) {
        // Solicitar nuevos datos del viaje
        echo "Ingrese el nuevo destino (actual: " . $viaje->getDestino() . ")// 0 para saltar paso: ";
        $destino = trim(fgets(STDIN));
        echo "Ingrese la nueva cantidad máxima de pasajeros (actual: " . $viaje->getMaxPasajeros() . ")// 0 para saltar paso: ";
        $maxPasajeros = trim(fgets(STDIN));
               // Actualizar la información del viaje
        if($destino !== 0){
            $viaje->setDestino($destino);
        }
        if($maxPasajeros !== 0){
              $viaje->setMaxPasajeros($maxPasajeros);
        }
 
      

        // Guardar los cambios en la base de datos
        if ($viaje->modificar()) {
            echo "Viaje modificado correctamente.\n";
            $viaje-> __toString();
        } else {
            echo "Error al modificar el viaje: " . $viaje->getmensajeoperacion() . "\n";
        }
    } else {
        echo "No se encontró un viaje con el ID ingresado.\n";
    }

    echo "Presione Enter para continuar...\n";
    fgets(STDIN);
}

// Función para eliminar un viaje
function eliminarViaje()
{
    echo "----- Eliminar Empresa -----\n";
    listarViajes();
    echo "Ingrese el Idviaje del viaje a eliminar: \n";
    $idViaje = trim(fgets(STDIN));

    $viaje = new Viaje();

    if ($viaje->Buscar($idViaje)) {
        // Confirmar la eliminación
        echo "Si elimina este viaje se eliminaran los Pasajero contenidos\n";
        echo "¿Está seguro de que desea eliminar la empresa? (S/N): \n";

        $confirmacion = strtoupper(trim(fgets(STDIN)));

        if ($confirmacion === 'S') {
            $pasajeros = $viaje->getpasajeros();

            foreach ($pasajeros as $pasajero) {
                $documento = $pasajero->getDocumento();
                if ($pasajero->Buscar($documento)) {
                    // Eliminar el pasajero de la base de datos
                    if ($pasajero->eliminar()) {
                        echo "Pasajero eliminado correctamente.\n";
                    } else {
                        echo "Error al eliminar el pasajero: " . $pasajero->getmensajeoperacion() . "\n";
                    }
                } else {
                    echo "No se encontró un pasajero con el documento ingresado.\n";
                }
            }
            $viaje->eliminar();
            echo " Viaje eliminado correctamente\n\n";
        }
        
    }
}
// Función para listar todos los viajes
function listarViajes()
{
    echo "----- Listar Viajes -----\n";

    // Obtener el listado de viajes desde la base de datos
    $viajes = Viaje::listar();

    // Verificar si hay viajes para mostrar
    if (!empty($viajes)) {
        // Mostrar la información de cada viaje
        foreach ($viajes as $viaje) {
            echo $viaje->__toString();
            echo "\n";
        }
    } else {
        echo "No hay viajes registrados.\n";
    }

    echo "Presione Enter para continuar...";
    fgets(STDIN);
}

function ingresarResponsableV()
{
    echo "----- Ingresar Responsable -----\n";

    // Solicitar datos del responsable
    echo "Ingrese el número de licencia: ";
    $numeroLicencia = trim(fgets(STDIN));
    echo "Ingrese el nombre: ";
    $nombre = trim(fgets(STDIN));
    echo "Ingrese el apellido: ";
    $apellido = trim(fgets(STDIN));

    // Crear instancia de ResponsableV y cargar los datos
    $responsable = new ResponsableV();
    $responsable->cargar(0, $numeroLicencia, $nombre, $apellido);

    // Insertar el responsable en la base de datos
    if ($responsable->insertar()) {
        echo "Responsable ingresado correctamente.\n";
    } else {
        echo "Error al ingresar el responsable: " . $responsable->getmensajeoperacion() . "\n";
    }

    echo "Presione Enter para continuar...";
    fgets(STDIN);
    return $responsable;
}

// Función para modificar un ResponsableV
function modificarResponsableV()
{
    echo "----- Modificar Responsable -----\n";
    listarResponsablesV();
    // Solicitar número de empleado del responsable a modificar
    echo "Ingrese el número de empleado del responsable a modificar: ";
    $numeroEmpleado = trim(fgets(STDIN));

    // Buscar el responsable en la base de datos
    $responsable = new ResponsableV();
    if ($responsable->Buscar($numeroEmpleado)) {
        // Solicitar nuevos datos del responsable
        echo "Ingrese el nuevo número de licencia (actual: " . $responsable->getNumeroLicencia() . ")// 0 para saltar paso: ";
        $numeroLicencia = trim(fgets(STDIN));
        echo "Ingrese el nuevo nombre (actual: " . $responsable->getNombre() . ")// 0 para saltar paso:  ";
        $nombre = trim(fgets(STDIN));
        echo "Ingrese el nuevo apellido (actual: " . $responsable->getApellido() . ")// 0 para saltar paso:  ";
        $apellido = trim(fgets(STDIN));

        // Actualizar la información del responsable
        if($numeroLicencia !== 0){
            $responsable->setNumeroLicencia($numeroLicencia);
        }
        if($nombre !== 0){
            $responsable->setNombre($nombre);
        }
        if($apellido !== 0){
            $responsable->setApellido($apellido);
        }
        // Guardar los cambios en la base de datos
        if ($responsable->modificar()) {
            echo "Responsable modificado correctamente.\n";
        } else {
            echo "Error al modificar el responsable: " . $responsable->getmensajeoperacion() . "\n";
        }
    } else {
        echo "No se encontró un responsable con el número de empleado ingresado.\n";
    }

    echo "Presione Enter para continuar...";
    fgets(STDIN);
}

// Función para eliminar un ResponsableV
function eliminarResponsableV()
{
    echo "----- Eliminar Responsable -----\n";

    listarResponsablesV();
    // Solicitar número de empleado del responsable a eliminar
    echo "Ingrese el número de empleado del responsable a eliminar: ";
    $numeroEmpleado = trim(fgets(STDIN));

    // Buscar el responsable en la base de datos
    $responsable = new ResponsableV();
    if ($responsable->Buscar($numeroEmpleado)) {
        // Confirmar la eliminación
        echo "¿Está seguro de que desea eliminar el responsable? (S/N): ";
        $confirmacion = strtoupper(trim(fgets(STDIN)));

        if ($confirmacion === 'S') {
            // Eliminar el responsable de la base de datos
            if ($responsable->eliminar()) {
                echo "Responsable eliminado correctamente.\n";
            } else {
                echo "Error al eliminar el responsable: " . $responsable->getmensajeoperacion() . "\n";
            }
        } else {
            echo "Operación cancelada.\n";
        }
    } else {
        echo "No se encontró un responsable con el número de empleado ingresado.\n";
    }

    echo "Presione Enter para continuar...";
    fgets(STDIN);
}

// Función para listar los ResponsablesV
function listarResponsablesV()
{

    echo "----- Listar Responsables -----\n";

    // Obtener el listado de responsables desde la base de datos
    $responsables = ResponsableV::listar();

    // Verificar si hay responsables para mostrar
    if (!empty($responsables)) {
        // Mostrar la información de cada responsable
        foreach ($responsables as $responsable) {
            echo $responsable->__toString();
            echo "\n";
        }
    } else {
        echo "No hay responsables registrados.\n";
    }

    echo "Presione Enter para continuar...";
    fgets(STDIN);
}

// Función para agregar un Pasajero
function ingresarPasajero()
{
    listarViajes();
    echo "Ingrese el codigo del viaje para el pasajero: ";
    $idViaje = trim(fgets(STDIN));
    $viaje = new Viaje();
    if ($viaje->Buscar($idViaje)) {
        $pasajero = ingresarPasajeroAux($viaje);
    } else {
        echo "No se encontró un viaje con el ID ingresado.\n";
    }
}
// Función para modificar un Pasajero
function modificarPasajero()
{
    echo "----- Modificar Pasajero -----\n";
    listarPasajeros();
    // Solicitar documento del pasajero a modificar
    echo "Ingrese el documento del pasajero a modificar: ";
    $documento = trim(fgets(STDIN));

    // Buscar el pasajero en la base de datos
    $pasajero = new Pasajero();
    if ($pasajero->Buscar($documento)) {
        // Solicitar nuevos datos del pasajero
        echo "Ingrese el nuevo nombre (actual: " . $pasajero->getNombre() . ")// 0 para saltar paso:";
        $nombre = trim(fgets(STDIN));
        echo "Ingrese el nuevo apellido (actual: " . $pasajero->getApellido() . ")// 0 para saltar paso:";
        $apellido = trim(fgets(STDIN));
        echo "Ingrese el nuevo teléfono (actual: " . $pasajero->getTelefono() . ")// 0 para saltar paso: ";
        $telefono = trim(fgets(STDIN));

        // Actualizar la información del pasajero
        if($nombre !== 0){
            $pasajero->setNombre($nombre);
        }
        if ($apellido !== 0){
            $pasajero->setApellido($apellido);
        }
        if($telefono){
            $pasajero->setTelefono($telefono);
        }
        // Guardar los cambios en la base de datos
        if ($pasajero->modificar()) {
            echo "Pasajero modificado correctamente.\n";
        } else {
            echo "Error al modificar el pasajero: " . $pasajero->getmensajeoperacion() . "\n";
        }
    } else {
        echo "No se encontró un pasajero con el documento ingresado.\n";
    }

    echo "Presione Enter para continuar...";
    fgets(STDIN);
}

// Función para eliminar un Pasajero
function eliminarPasajero()
{
    echo "----- Eliminar Pasajero -----\n";
    listarPasajeros();
    // Solicitar documento del pasajero a eliminar
    echo "Ingrese el documento del pasajero a eliminar: ";
    $documento = trim(fgets(STDIN));

    // Buscar el pasajero en la base de datos
    $pasajero = new Pasajero();
    if ($pasajero->Buscar($documento)) {
        // Eliminar el pasajero de la base de datos
        if ($pasajero->eliminar()) {
            echo "Pasajero eliminado correctamente.\n";
        } else {
            echo "Error al eliminar el pasajero: " . $pasajero->getmensajeoperacion() . "\n";
        }
    } else {
        echo "No se encontró un pasajero con el documento ingresado.\n";
    }

    echo "Presione Enter para continuar...";
    fgets(STDIN);
}

// Función para listar los Pasajeros
function listarPasajeros()
{

    echo "----- Listar Pasajeros -----\n";

    // Obtener el listado de responsables desde la base de datos
    $pasajeros = Pasajero::listar();

    // Verificar si hay responsables para mostrar
    if (!empty($pasajeros)) {
        // Mostrar la información de cada responsable
        foreach ($pasajeros as $pasajero) {
            echo $pasajero->__toString();
            echo "\n";
        }
    } else {
        echo "No hay pasajeros registrados.\n";
    }

    echo "Presione Enter para continuar...";
    fgets(STDIN);
}

// Función principal del programa
function menuPrincipal()
{
    $opcion = 0;

    while ($opcion != 5) {
        mostrarMenuPrincipal();
        $opcion = intval(trim(fgets(STDIN)));

        switch ($opcion) {
            case 1:
                mostrarMenuEmpresa();
                $opcionEmpresa = intval(trim(fgets(STDIN)));

                switch ($opcionEmpresa) {
                    case 1:
                        crearEmpresa();
                        break;
                    case 2:
                        modificarEmpresa();
                        break;
                    case 3:
                        eliminarEmpresa();
                        break;
                    case 4:
                        listarEmpresas();
                        break;
                    default:
                        echo "Opción inválida. Intente nuevamente.\n";
                        break;
                }

                break;
            case 2:
                mostrarMenuViaje();
                $opcionViaje = intval(trim(fgets(STDIN)));

                switch ($opcionViaje) {
                    case 1:
                        IngresarViaje();
                        break;
                    case 2:
                        modificarViaje();
                        break;
                    case 3:
                        eliminarViaje();
                        break;
                    case 4:
                        listarViajes();
                        break;
                    default:
                        echo "Opción inválida. Intente nuevamente.\n";
                        break;
                }

                break;
            case 3:
                mostrarMenuResponsableV();
                $opcionResponsable = intval(trim(fgets(STDIN)));

                switch ($opcionResponsable) {
                    case 1:
                        ingresarResponsableV();
                        break;
                    case 2:
                        modificarResponsableV();
                        break;
                    case 3:
                        eliminarResponsableV();
                        break;
                    case 4:
                        listarResponsablesV();
                        break;
                    default:
                        echo "Opción inválida. Intente nuevamente.\n";
                        
                        break;
                }

                break;
            case 4:
                mostrarMenuPasajero();
                $opcionPasajero = intval(trim(fgets(STDIN)));

                switch ($opcionPasajero) {
                    case 1:
                        IngresarPasajero();
                        break;
                    case 2:
                        modificarPasajero();
                        break;
                    case 3:
                        eliminarPasajero();
                        break;
                    case 4:
                        listarPasajeros();
                        break;
                    default:
                        echo "Opción inválida. Intente nuevamente.\n";
                        break;
                }

                break;
            case 5:
                echo "Saliendo del programa...\n";
                break;
            default:
                echo "Opción inválida. Intente nuevamente.\n";
                break;
        }
    }
}

// Ejecutar la función principal
menuPrincipal();
