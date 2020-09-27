<?php
require_once('consultas_extension.php');

class ci_inscriptos_curso_online extends toba_ci
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

	//VER: http://www.exa.unicen.edu.ar/reportes-guarani-grado/curso-ingreso/inscriptos.php?reporte=inscriptos
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{	
		if (isset($this->s__datos_filtro)) 
		{	
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['anio_academico'];
			$datos = consultas_extension::get_datos_inscriptos_online($anio);
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