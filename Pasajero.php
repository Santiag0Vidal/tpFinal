<?php
class Pasajero {
    private $nombre;
    private $apellido;
    private $documento;
    private $telefono;
    private $viaje;
    private $mensajeoperacion;

    public function __construct() {
        $this->nombre = "";
        $this->apellido = "";
        $this->documento = "";
        $this->telefono = "";
        $this->viaje = new Viaje();

    }

    public function cargar($documento,$nombre,$apellido, $telefono){	
	    $this->setDocumento($documento);
		$this->setNombre($nombre);
		$this->setApellido($apellido);
        $this->setTelefono($telefono);
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getApellido() {
        return $this->apellido;
    }

    public function getDocumento() {
        return $this->documento;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function getViaje() {
        return $this->viaje;
    }

    public function getmensajeoperacion(){
		return $this->mensajeoperacion ;
	}

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setApellido($apellido) {
        $this->apellido = $apellido;
    }

    public function setDocumento($documento) {
        $this->documento = $documento;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    public function setViaje($viaje){
        $this->viaje = $viaje;
    }

    public function setmensajeoperacion($mensajeoperacion){
		$this->mensajeoperacion=$mensajeoperacion;

	}

    public function Buscar($documento){
		$base=new BaseDatos();
		$consultaPasajero="Select * from pasajero where pdocumento=".$documento;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaPasajero)){
				if($row2=$base->Registro()){
					$viaje = new Viaje();
                    $viaje->Buscar($row2['idviaje']);
                    $this->cargar( $documento,$row2['pnombre'], $row2['papellido'], $row2['ptelefono']);
                    $this->setViaje($viaje);			
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
	    $arregloPasajeros = null;
		$base=new BaseDatos();
		$consultaPasajero="Select * from pasajero ";
		if ($condicion!=""){
		    $consultaPasajero=$consultaPasajero." where ".$condicion;
		}
		$consultaPasajero.=" order by pdocumento ";
		if($base->Iniciar()){
			if($base->Ejecutar($consultaPasajero)){				
				$arregloPasajeros= array();
				while($row2=$base->Registro()){  
                    $pasajero = new Pasajero();
					$pasajero->cargar($row2['pdocumento'],$row2['pnombre'],$row2['papellido'], $row2['ptelefono']);
					array_push($arregloPasajeros,$pasajero);
	
				}
				
			
		 	}	else {
		 			$this->setmensajeoperacion($base->getError());
		 		
			}
		 }	else {
		 		$this->setmensajeoperacion($base->getError());
		 	
		 }	
		 return $arregloPasajeros;
	}	


	
	public function insertar(){
		$base=new BaseDatos();
		$resp= false;
		$consultaInsertar="INSERT INTO pasajero(pdocumento, pnombre, papellido, ptelefono, idviaje) 
				VALUES ('".$this->getDocumento()."','".$this->getNombre()."','".$this->getApellido()."',".$this->getTelefono().", ".$this->getViaje()->getIdViaje().")";
		
		if($base->Iniciar()){

			if($base->Ejecutar($consultaInsertar)){
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
		$consultaModifica= "UPDATE pasajero SET pdocumento='".$this->getDocumento()."',pnombre='".$this->getNombre()."',papellido ='".$this->getApellido()."',ptelefono=".$this->getTelefono()." WHERE pdocumento=".$this->getDocumento();
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
				$consultaBorra="DELETE FROM pasajero WHERE pdocumento=".$this->getDocumento();
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
        $informacion = "Nombre: " . $this->getNombre() . "\n";
        $informacion .= "Apellido: " . $this->getApellido() . "\n";
        $informacion .= "Documento: " . $this->getDocumento() . "\n";
        $informacion .= "Teléfono: " . $this->getTelefono() . "\n";
    
        return $informacion;
    }     
}
