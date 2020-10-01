<?php
require_once('MisConsultas.php');

class ci_alumnos_ingresantes extends toba_ci
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
			$datos = $this->get_ingresantes($this->s__datos_filtro);
			$cuadro->set_datos($datos);
		} 
	}

	// function evt__cuadro__seleccion($datos)
	// {
	// 	$this->dep('datos')->cargar($datos);
	// 	$this->set_pantalla('pant_edicion');
	// }

	//---- Funciones -------------------------------------------------------------------


	function get_ingresantes($filtro=array())
	{
		$where = array();
		
		if (isset($filtro['carrera'])) {
			$where[] = "sga_alumnos.carrera LIKE ".quote("%{$filtro['carrera']}%");
		}
		
		if (isset($filtro['ingreso'])) {
			$where[] = "sga_periodo_insc.anio_academico = ".$filtro['ingreso'];
		}

		$sql = 
			'SELECT 
				sga_sedes.nombre AS sede,
				sga_carreras.nombre AS carrera, 
				sga_personas.apellido || ", " || sga_personas.nombres AS nombre, 
				sga_personas.nro_documento, 
				sga_periodo_insc.anio_academico AS ingreso
			FROM 
				sga_personas, sga_alumnos, sga_carrera_aspira, sga_periodo_insc, sga_carreras, sga_sedes
			WHERE 
				sga_personas.nro_inscripcion = sga_alumnos.nro_inscripcion AND
				sga_alumnos.nro_inscripcion = sga_carrera_aspira.nro_inscripcion AND
				sga_alumnos.carrera = sga_carrera_aspira.carrera AND 
				sga_carrera_aspira.periodo_inscripcio = sga_periodo_insc.periodo_inscripcio AND
				sga_carrera_aspira.carrera = sga_carreras.carrera AND
				sga_sedes.sede = sga_alumnos.sede AND
				sga_alumnos.fecha_ingreso <= ALL (SELECT fecha_ingreso 
																FROM sga_alumnos A2 
																WHERE sga_alumnos.legajo = A2.legajo)';
				
		if (count($where)>0) {
			$sql = sql_concatenar_where($sql, $where);
		}
                
                $sql .= " ORDER BY 5,2,3 ";

		$resultado = MisConsultas::query($sql);
		
		return $resultado;
		
	}	


	//---- Formulario -------------------------------------------------------------------

	// function conf__formulario(toba_ei_formulario $form)
	// {
	// 	if ($this->dep('datos')->esta_cargada()) {
	// 		$form->set_datos($this->dep('datos')->tabla('prueba')->get());
	// 	} else {
	// 		$this->pantalla()->eliminar_evento('eliminar');
	// 	}
	// }

	// function evt__formulario__modificacion($datos)
	// {
	// 	$this->dep('datos')->tabla('prueba')->set($datos);
	// }

	// function resetear()
	// {
	// 	$this->dep('datos')->resetear();
	// 	$this->set_pantalla('pant_seleccion');
	// }

	// //---- EVENTOS CI -------------------------------------------------------------------

	// function evt__agregar()
	// {
	// 	$this->set_pantalla('pant_edicion');
	// }

	// function evt__volver()
	// {
	// 	$this->resetear();
	// }

	// function evt__eliminar()
	// {
	// 	$this->dep('datos')->eliminar_todo();
	// 	$this->resetear();
	// }

	// function evt__guardar()
	// {
	// 	$this->dep('datos')->sincronizar();
	// 	$this->resetear();
	// }

}

?>