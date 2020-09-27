<?php
require_once('consultas_no_ingresantes.php');

class ci_informe_no_ingresantes extends toba_ci
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

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['ANIO_ACADEMICO'];
			$total_no_ingresantes = consultas_no_ingresantes::get_cantidad_total_no_ingresantes($anio);
			$form->set_datos($total_no_ingresantes);
		}
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_no_ingresantes_carrera ------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_no_ingresantes_carrera(Reportes_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['ANIO_ACADEMICO'];
			$ingresantes = consultas_no_ingresantes::get_no_ingresantes_x_carrera($anio);
			$cuadro->set_datos($ingresantes);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_no_ingres_carrera_col -------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_no_ingres_carrera_col(Reportes_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['ANIO_ACADEMICO'];
			$ingresantes = consultas_no_ingresantes::get_no_ingresantes_x_carrera_x_colegio_Tandil($anio);
			$cuadro->set_datos($ingresantes);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_no_ingres_carrera_loc -------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_no_ingres_carrera_loc(Reportes_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['ANIO_ACADEMICO'];
			$no_ingresantes = consultas_no_ingresantes::get_no_ingresantes_x_carrera_x_localidad($anio);
			$cuadro->set_datos($no_ingresantes);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_no_ingresantes --------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_no_ingresantes(Reportes_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['ANIO_ACADEMICO'];
			$no_ingresantes = consultas_no_ingresantes::get_cant_veces_no_aprobaron($anio);
			$cuadro->set_datos($no_ingresantes);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_no_ingres_col ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_no_ingres_col(Reportes_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['ANIO_ACADEMICO'];
			$no_ingresantes = consultas_no_ingresantes::get_no_ingresantes_x_colegio_Tandil($anio);
			$cuadro->set_datos($no_ingresantes);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro_no_ingres_loc ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function conf__cuadro_no_ingres_loc(Reportes_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['ANIO_ACADEMICO'];
			$no_ingresantes = consultas_no_ingresantes::get_no_ingrasantes_x_localidad($anio);
			$cuadro->set_datos($no_ingresantes);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}

}
?>