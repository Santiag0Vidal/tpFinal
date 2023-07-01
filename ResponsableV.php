<?php
class ResponsableV {
    private $numeroEmpleado;
    private $numeroLicencia;
    private $nombre;
    private $apellido;
    private $mensajeoperacion;

    public function __construct() {
        $this->numeroEmpleado = "";
        $this->numeroLicencia = "";
        $this->nombre = "";
        $this->apellido = "";

    }

    public function cargar($numeroEmpleado,$numeroLicencia,$nombre,$apellido){	
	    $this->setNumeroEmpleado($numeroEmpleado);
        $this->setNumeroLicencia($numeroLicencia);
		$this->setNombre($nombre);
		$this->setApellido($apellido);

    }

    public function getNumeroEmpleado() {
        return $this->numeroEmpleado;
    }

    public function getNumeroLicencia() {
        return $this->numeroLicencia;
    }
    
    public function getNombre() {
        return $this->nombre;
    }
    public function getApellido() {
        return $this->apellido;
    }

    public function getmensajeoperacion(){
		return $this->mensajeoperacion ;
	}

    public function setNumeroEmpleado($numeroEmpleado) {
        $this->numeroEmpleado = $numeroEmpleado;
    }

    public function setNumeroLicencia($numeroLicencia) {
        $this->numeroLicencia = $numeroLicencia;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setApellido($apellido) {
        $this->apellido = $apellido;
    }

    public function setmensajeoperacion($mensajeoperacion){
		$this->mensajeoperacion=$mensajeoperacion;

	}


    public function Buscar($numeroEmpleado){
		$base=new BaseDatos();
        $consultaPersona = "Select * from responsable where rnumeroempleado=".$numeroEmpleado;

		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaPersona)){
				if($row2=$base->Registro()){
					$this->cargar($numeroEmpleado,$row2['rnumerolicencia'],$row2['rnombre'],$row2['rapellido']);			
					$resp= true;
				}				
			
		 	}	else {
		 			$this->setmensajeoperacion($base->getError());
		 		
			}
		 }	else {
		 		$this->setmensajeoperacion($base->getError());
		 	
		 }		
		 return $resp;
	}	
    

	public static function listar($condicion=""){
	    $arregloResponsable = null;
		$base=new BaseDatos();
		$consultaResponsable="Select * from responsable ";
		if ($condicion!=""){
		    $consultaResponsable .= " where ".$condicion;
		}
		$consultaResponsable .= " order by rnumeroempleado ";
		if($base->Iniciar()){
			if($base->Ejecutar($consultaResponsable)){				
				$arregloResponsable= array();
				while($row2=$base->Registro()){
					
					$responsable=new ResponsableV();
					$responsable->cargar($row2['rnumeroempleado'],$row2['rnumerolicencia'],$row2['rnombre'],$row2['rapellido']);
					array_push($arregloResponsable,$responsable);
	
				}
				
			
		 	}	else {
		 			$this->setmensajeoperacion($base->getError());
		 		
			}
		 }	else {
		 		$this->setmensajeoperacion($base->getError());
		 	
		 }	
		 return $arregloResponsable;
	}	


	
	public function insertar(){
		$base=new BaseDatos();
		$resp= false;
        $consultaInsertar = "INSERT INTO responsable(rnumerolicencia, rnombre, rapellido) 
        VALUES (".$this->getNumeroLicencia().",'".$this->getNombre()."','".$this->getApellido()."')";

		
		if($base->Iniciar()){

			if($id = $base->devuelveIDInsercion($consultaInsertar)){
                $this->setNumeroEmpleado($id);
			    $resp=  true;

			}	else {
					$this->setmensajeoperacion($base->getError());
					
			}

		} else {
				$this->setmensajeoperacion($base->getError());
			
		}
		return $resp;
	}
	
	
	
	public function modificar(){
	    $resp =false; 
	    $base=new BaseDatos();
        $consultaModifica = "UPDATE responsable SET rnumerolicencia='".$this->getNumeroLicencia()."', rnombre='".$this->getNombre()."', rapellido='".$this->getApellido()."' WHERE rnumeroempleado=".$this->getNumeroEmpleado();

		if($base->Iniciar()){
			if($base->Ejecutar($consultaModifica)){
			    $resp=  true;
			}else{
				$this->setmensajeoperacion($base->getError());
				
			}
		}else{
				$this->setmensajeoperacion($base->getError());
			
		}
		return $resp;
	}
	
	public function eliminar(){
		$base=new BaseDatos();
		$resp=false;
		if($base->Iniciar()){
                $consultaBorra = "DELETE FROM responsable WHERE rnumeroempleado=".$this->getNumeroEmpleado();

				if($base->Ejecutar($consultaBorra)){
				    $resp=  true;
				}else{
						$this->setmensajeoperacion($base->getError());
					
				}
		}else{
				$this->setmensajeoperacion($base->getError());
			
		}
		return $resp; 
	}


    public function __toString() {
        
        $informacion = "Número de Empleado: " . $this->getNumeroEmpleado() . "\n";
        $informacion .= "Número de Licencia: " . $this->getNumeroLicencia() . "\n";
        $informacion .= "Nombre: " . $this->getNombre() . "\n";
        $informacion .= "Apellido: " . $this->getApellido() . "\n";
        return $informacion;
    }
    
}
