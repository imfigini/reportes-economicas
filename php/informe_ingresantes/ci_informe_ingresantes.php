<?php
require_once('consultas_ingresantes.php');

class ci_informe_ingresantes extends toba_ci
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
	//EN TODOS LOS CASOS:
	//Se consideran sólo ingresantes nuevos, se descartan alumnos que ya venían de otra carrera o de otra facultad o universidad. 
        //Sólo se consideran los que no tienen ninguna equivalencia. 
	
	//Cantidad de ingresantes por carrera
	function conf__cuadro_ingresantes_carrera(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['ANIO_ACADEMICO'];
			$ingresantes = consultas_ingresantes::get_ingresantes_x_carrera($anio);
			$cuadro->set_datos($ingresantes);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}
	
	//Cantidad de ingresantes por carrera y localidad de colegio secundario que egresaron
	function conf__cuadro_ingres_carrera_loc(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['ANIO_ACADEMICO'];
			$ingresantes = consultas_ingresantes::get_ingresantes_x_carrera_x_localidad($anio);
			$cuadro->set_datos($ingresantes);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}
	
	//Cantidad de ingresantes por localidad de colegio secundario que egresaron (sin importar carrera)
	function conf__cuadro_ingres_loc(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['ANIO_ACADEMICO'];
			$ingresantes = consultas_ingresantes::get_ingrasantes_x_localidad($anio);
			$cuadro->set_datos($ingresantes);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}
	
	//Cantidad de ingresantes por carrera y colegio secundario de Tandil
	function conf__cuadro_ingres_carrera_col(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['ANIO_ACADEMICO'];
			$ingresantes = consultas_ingresantes::get_ingresantes_x_carrera_x_colegio_Tandil($anio);
			$cuadro->set_datos($ingresantes);
		} 
		else {
			$cuadro->limpiar_columnas();
		}
	}

	//Cantidad de ingresantes por colegio secundario que egresaron de Tandil (sin importar carrera)
	function conf__cuadro_ingres_col(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			$anio = $filtro['ANIO_ACADEMICO'];
			$ingresantes = consultas_ingresantes::get_ingresantes_x_colegio_Tandil($anio);
			$cuadro->set_datos($ingresantes);
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
			$anio = $filtro['ANIO_ACADEMICO'];
			$total_ingresantes = consultas_ingresantes::get_cantidad_total_ingresantes($anio);
			$form->set_datos($total_ingresantes);
		}
	}

}

?>