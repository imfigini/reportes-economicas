<?php
require_once ('MisConsultas.php');

class cn_guarani extends Reportes_cn
{
    protected $s__clave_actual;
    
    function resetear()
    {
        $this->s__clave_actual = null;
    }
    
    //--------------------------------------------------------------------------
    //---- Manejo de datos -----------------------------------------------------
    //--------------------------------------------------------------------------
    public function get_clave_actual()
    {
        return $this->s__clave_actual;
    }
    
    function cargar($datos)
    {
        $this->s__clave_actual = $datos;
    }
    
    function get_hist_academica()
    {
        $datos = MisConsultas::getHistoriaAcademica($this->s__clave_actual);
        $resultado = array();
        foreach($datos AS $dato)
        {
            if ($dato[4] == '211')
            {
                $resultado[] = $dato;
            }
        }
        return $resultado;
    }
    
    function get_cursadas()
    {
        $filtro = $this->s__clave_actual;
        $filtro['CARRERA'] = 211;
        $datos = MisConsultas::getCursadas($filtro);
        return $datos;
    }

    function get_carreras()
    {
        $datos = MisConsultas::getCarrerasPlan($this->s__clave_actual);
        
        return $datos;
    }

	
    
}

?>