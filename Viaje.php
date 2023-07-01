<?php
class Viaje {
    private $idViaje;
    private $destino;
    private $maxPasajeros;
    private $pasajeros;
    private $responsable;
    private $empresa;
    private $mensajeoperacion;
	private $valor;

    public function __construct() {
        $this->idViaje = 0;
        $this->destino = "";
        $this->maxPasajeros = 0;
        $this->pasajeros = array();
        $this->responsable = new ResponsableV;
        $this->empresa = new Empresa();
		$this->valor = 0;
    }

	public function cargar($idViaje,$destino,$maxPasajeros,$responsable, $valor){	
	    $this->setIdViaje($idViaje);
		$this->setDestino($destino);
		$this->setMaxPasajeros($maxPasajeros);
		$this->setPasajeros(Pasajero::listar('idviaje =' . $idViaje));
        $this->setResponsable($responsable);
		$this->setValor ($valor);
		

    }

    public function getIdViaje() {
        return $this->idViaje;
    }
	public function getValor() {
        return $this->valor;
    }
	public function setValor($valor) {
        $this->valor = $valor;
    }
    public function getDestino() {
        return $this->destino;
    }

    public function getMaxPasajeros() {
        return $this->maxPasajeros;
    }

    public function getPasajeros() {
        return $this->pasajeros;
    }

    public function getResponsable() {
        return $this->responsable;
    }
    public function getEmpresa() {
        return $this->empresa;
    }
    
    public function getmensajeoperacion(){
		return $this->mensajeoperacion ;
    }

    public function setIdViaje($idViaje) {
        $this->idViaje = $idViaje;
    }

    public function setDestino($destino) {
        $this->destino = $destino;
    }

    public function setMaxPasajeros($maxPasajeros) {
        $this->maxPasajeros = $maxPasajeros;
    }

    public function setPasajeros($pasajeros){
        $this->pasajeros = $pasajeros;
    }

    public function setResponsable($responsable) {
        $this->responsable = $responsable;
    }
    public function setEmpresa($empresa){
        $this->empresa = $empresa;
    }

    public function setmensajeoperacion($mensajeoperacion){
		$this->mensajeoperacion=$mensajeoperacion;

	}

    public function Buscar($idViaje){
		$base=new BaseDatos();
		$consultaPersona="Select * from viaje where idviaje=".$idViaje;
		$resp= false;
		if($base->Iniciar()){
			if($base->Ejecutar($consultaPersona)){
				if($row2=$base->Registro()){
					$resp = new ResponsableV();
                    $resp->Buscar($row2['rnumeroempleado']);
					$emp = new Empresa();
                    $emp->Buscar($row2['idempresa']);
					$this->cargar($idViaje,$row2['vdestino'],$row2['vcantmaxpasajeros'],$resp,$row2['vimporte'],$emp);

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
	    $arregloViaje = null;
		$base=new BaseDatos();
		$consultarViaje="Select * from viaje ";
		if ($condicion!=""){
		    $consultarViaje=$consultarViaje." where ".$condicion;
		}
		$consultarViaje.=" order by idviaje ";
		//echo $consultarViaje;
		if($base->Iniciar()){
			if($base->Ejecutar($consultarViaje)){				
				$arregloViaje= array();
				while($row2=$base->Registro()){
					$resp = new ResponsableV();
                    $resp->Buscar($row2['rnumeroempleado']);
                    $viaje=new Viaje();
					$emp = new Empresa();
                    $emp->Buscar($row2['idempresa']);
					$viaje->cargar($row2['idviaje'],$row2['vdestino'],$row2['vcantmaxpasajeros'],$resp, $row2['vimporte']);
					array_push($arregloViaje,$viaje);
	
				}
				
			
		 	}	else {
		 			$this->setmensajeoperacion($base->getError());
		 		
			}
		 }	else {
		 		$this->setmensajeoperacion($base->getError());
		 	
		 }	
		 return $arregloViaje;
	}	


	
	public function insertar()
	{
		$base = new BaseDatos();
		$resp = false;
		$consultaInsertar = "INSERT INTO viaje (vdestino, vcantmaxpasajeros, idempresa, rnumeroempleado, vimporte)
                     VALUES ('" . $this->getDestino() . "', " . $this->getMaxPasajeros() . ", " . 
					 $this->getEmpresa()->getIdEmpresa() . ", " . $this->getResponsable()->getNumeroEmpleado() . ", " . $this->getValor() . ")";

		if ($base->Iniciar()) {

			if ($id = $base->devuelveIDInsercion($consultaInsertar)) {
				$this->setIdViaje($id);
				$resp =  true;
			} else {
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
        
		if($base->Iniciar()){
            $consultaModifica = "UPDATE viaje SET vdestino='".$this->getDestino()."', vcantmaxpasajeros=".$this->getMaxPasajeros()." WHERE idviaje=".$this->getIdViaje();
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
				$consultaBorra="DELETE FROM viaje WHERE idviaje=".$this->getIdViaje();
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
        
        $informacion = "Código: " . $this->getidViaje() . "\n";
        $informacion .= "Destino: " . $this->getDestino() . "\n";
        $informacion .= "Cantidad máxima de pasajeros: " . $this->getMaxPasajeros() . "\n";

        if (!empty($this->pasajeros)) {
            $informacion .= "Pasajeros:\n";
            foreach ($this->getPasajeros() as $pasajero) {
                $informacion .= "\n-----------\n";
                $informacion .= $pasajero->__toString();
            }
        } else {
            $informacion .= "No hay pasajeros registrados.\n";
        }

        $informacion .= "Responsable del viaje: ".$this->getResponsable();
        
        return $informacion;
    }
	
	
}



        
