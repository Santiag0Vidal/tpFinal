<?php
class Empresa
{
    private $idEmpresa;
    private $nombre;
    private $direccion;
    private $viajes;
    private $mensajeoperacion;

    public function __construct()
    {
        $this->idEmpresa ="";
        $this->nombre = "";
        $this->direccion= "";
        $this->viajes = array();
    }

    public function cargar($idEmpresa,$nombre,$direccion){	
	    $this->setIdEmpresa($idEmpresa);
		$this->setNombre($nombre);
		$this->setDireccion($direccion);
		$this->setViajes(Viaje::listar('idempresa =' . $this->getIdEmpresa()));
    }

    public function getIdEmpresa(){
        return $this->idEmpresa;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function getDireccion(){
        return $this->direccion;
    }

    public function getViajes() {
        return $this->viajes;
    }
	
	public function getmensajeoperacion(){
		return $this->mensajeoperacion ;
	}

    public function setIdEmpresa($idEmpresa){
        $this->idEmpresa = $idEmpresa;
    }

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }

    public function setDireccion($direccion){
        $this->direccion = $direccion;
    }

    public function setViajes($viajes){
        $this->viajes = $viajes;
    }
	
	public function setmensajeoperacion($mensajeoperacion){
		$this->mensajeoperacion=$mensajeoperacion;

	}


    public function Buscar($idEmpresa){
		$base=new BaseDatos();
		$consultaPersona="Select * from empresa where idempresa=".$idEmpresa;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaPersona)){
				if($row2=$base->Registro()){
					$this->cargar($idEmpresa, $row2['enombre'],$row2['edireccion']);
					
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
	    $arregloEmpresa = null;
		$base=new BaseDatos();
		$consultaEmpresa="Select * from empresa ";
		if ($condicion!=""){
		    $consultaEmpresa=$consultaEmpresa." where ".$condicion;
		}
		$consultaEmpresa.=" order by idempresa ";
		//echo $consultaEmpresa;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaEmpresa)){				
				$arregloEmpresa= array();
				while($row2=$base->Registro()){
                    $empresa=new Empresa();
					$empresa->cargar($row2['idempresa'],$row2['enombre'],$row2['edireccion']);
					array_push($arregloEmpresa,$empresa);
	
				}
				
			
		 	}	else {
		 			$this->setmensajeoperacion($base->getError());
		 		
			}
		 }	else {
		 		$this->setmensajeoperacion($base->getError());
		 	
		 }	
		 return $arregloEmpresa;
	}	


	
	public function insertar(){
		$base=new BaseDatos();
		$resp= false;
		$consultaInsertar="INSERT INTO empresa(enombre, edireccion) 
				VALUES ('".$this->getNombre()."','".$this->getDireccion()."')";
		
		if($base->Iniciar()){

			if($id = $base->devuelveIDInsercion($consultaInsertar)){
                $this->setIdEmpresa($id);
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
		$consultaModifica="UPDATE empresa SET enombre='".$this->getNombre()."',edireccion='".$this->getDireccion()."' WHERE idempresa=".$this->getIdEmpresa();
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
				$consultaBorra="DELETE FROM empresa WHERE idempresa=".$this->getIdEmpresa();
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




    public function __toString(){
    $informacion = "Empresa [";
    $informacion .= "idEmpresa=" . $this->getIdEmpresa() . ", ";
    $informacion .= "nombre=" . $this->getNombre() . ", ";
    $informacion .= "direccion=" . $this->getDireccion() . ", ";
    $informacion .= "viajes=[";
    
    foreach ($this->getViajes() as $viaje) {
        $informacion .= $viaje . ", ";
    }
    $informacion = rtrim($informacion, ", "); // Eliminar la última coma y espacio
    
    $informacion .= "]]";
    
    return $informacion;
}






}
