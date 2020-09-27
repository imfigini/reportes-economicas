<?php
require_once ('consultas.php');

class ci_alumnos_con_correlat_aprob extends toba_ci
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
			$datos = consultas::get_alumnos_con_correlat_aprob($filtro);
			$cuadro->set_datos($datos);
		} 
		else 
		{
			$cuadro->limpiar_columnas();
		}
	}


	//---- EVENTOS CI -------------------------------------------------------------------

	function evt__volver()
	{
		$this->resetear();
	}
}

?>