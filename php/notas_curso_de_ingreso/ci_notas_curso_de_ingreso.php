<?php
require_once('consultas.php');

class ci_notas_curso_de_ingreso extends toba_ci
{
    protected $s__datos_filtro;


    //---- Filtro -----------------------------------------------------------------------

    function conf__filtro(toba_ei_formulario $filtro)
    {
        if (isset($this->s__datos_filtro)) {
                $filtro->set_datos($this->s__datos_filtro);
        }
    }

    function evt__filtro__filtrar($datos)
    {
        $this->s__datos_filtro = $datos;
    }

    function evt__filtro__cancelar()
    {
        unset($this->s__datos_filtro);
    }

    //---- Cuadro -----------------------------------------------------------------------

    function conf__cuadro(toba_ei_cuadro $cuadro)
    {
        if (isset($this->s__datos_filtro)) 
        {
            $filtro = $this->s__datos_filtro;
            $datos = consultas::get_alumnos($filtro);
            $cuadro->set_datos($datos);
        }
        else 
        {
            $datos = consultas::get_alumnos();
            $cuadro->set_datos($datos);
        }
    }

}