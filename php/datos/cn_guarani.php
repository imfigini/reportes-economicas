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
        
        return $datos;
    }
    
    function get_cursadas()
    {
        $datos = MisConsultas::getCursadas($this->s__clave_actual);
        
        return $datos;
    }

    function get_insc_cursadas()
    {
        $datos = MisConsultas::getInscripcionCursadas($this->s__clave_actual);
        
        return $datos;
    }

    function get_carreras()
    {
        $datos = MisConsultas::getCarrerasPlan($this->s__clave_actual);
        $nro_inscripcion = $this->s__clave_actual['NRO_INSCRIPCION'];
        $resultado = array();
        foreach($datos as $dato)
        {
            $carrera = $dato['CARRERA_CODIGO'];
            $dato['PORC_AVANCE'] = MisConsultas::add_porcentaje_avance($nro_inscripcion, $carrera);
            $resultado[] = $dato;
        }
        return $resultado;
    }
    
    function get_porc_avance()
    {
        return MisConsultas::get_porcentaje_avance();
    }
    
}

?>