<?php
require_once('MisConsultas.php');

class ci_desaprobados_curso_de_ingreso extends toba_ci
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
			$anio = $this->s__datos_filtro['anio'];
			$datos = MisConsultas::get_curso_ingreso_desaprobados($anio);
			$cuadro->set_datos($datos);
		} 
		else 
		{
			$cuadro->limpiar_columnas();
		}
	}
}

?>