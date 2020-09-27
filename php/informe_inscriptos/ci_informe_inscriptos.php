<?php
require_once('consultas_inscriptos.php');

class ci_informe_inscriptos extends toba_ci
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
		consultas_inscriptos::pre_procesar_inscriptos($datos['anio_academico']); 
	}

	function evt__filtro__cancelar()
	{
		unset($this->s__datos_filtro);
	}

	//---- Cuadro -----------------------------------------------------------------------

	//Cantidad de inscriptos por carrera
	function conf__cuadro_inscriptos_carrera(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['anio_academico'];
			$inscr = consultas_inscriptos::get_inscriptos_x_carrera($anio);
			$cuadro->set_datos($inscr);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}
	
	//Cantidad de inscriptos por carrera y localidad de colegio secundario que egresaron
	function conf__cuadro_inscrip_carrera_loc(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['anio_academico'];
			$inscr = consultas_inscriptos::get_inscriptos_x_carrera_x_localidad($anio);
			$cuadro->set_datos($inscr);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}
	
	//Cantidad de inscriptos por localidad de colegio secundario que egresaron (sin importar carrera)
	function conf__cuadro_inscriptos_loc(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['anio_academico'];
			$inscr = consultas_inscriptos::get_inscriptos_x_localidad($anio);
			$cuadro->set_datos($inscr);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}
	
	//Cantidad de inscriptos por carrera y colegio secundario de Tandil
	function conf__cuadro_inscrip_carrera_col(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['anio_academico'];
			$inscr = consultas_inscriptos::get_inscriptos_x_carrera_x_colegio_Tandil($anio);
			$cuadro->set_datos($inscr);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}

	//Cantidad de inscriptos por colegio secundario que egresaron de Tandil (sin importar carrera)
	function conf__cuadro_inscriptos_col(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['anio_academico'];
			$inscr = consultas_inscriptos::get_inscriptos_x_colegio_Tandil($anio);
			$cuadro->set_datos($inscr);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}
	
	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['anio_academico'];
			$cant = consultas_inscriptos::get_cantidad_total_inscriptos($anio);
			$form->set_datos($cant);
		}
		else
			$this->pantalla()->eliminar_evento('eliminar');
	}

	function evt__formulario__modificacion($datos)
	{
		$this->dep('datos')->tabla('prueba')->set($datos);
	}

	function resetear()
	{
		$this->dep('datos')->resetear();
		$this->set_pantalla('pant_seleccion');
	}

}
?>