<?php
require_once('consultas_extension.php');

class ci_materias_regularizadas extends toba_ci
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
			$anio = $filtro['anio_ingresantes'];
			$materia = $filtro['materia'];
			$cuatrimestre = $filtro['cuatrimestre'];
			$condicion = $filtro['condicion'];
			$datos = consultas_extension::get_alumnos_cohorte_cursada($materia, $anio, $cuatrimestre, $condicion);
			$cuadro->set_datos($datos);
		} 
		else 
			$cuadro->limpiar_columnas();
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	//---- EVENTOS CI -------------------------------------------------------------------

	function evt__agregar()
	{
		$this->set_pantalla('pant_edicion');
	}

	function evt__volver()
	{
		$this->resetear();
	}

	function evt__eliminar()
	{
		$this->dep('datos')->eliminar_todo();
		$this->resetear();
	}

	function evt__guardar()
	{
		$this->dep('datos')->sincronizar();
		$this->resetear();
	}

}

?>