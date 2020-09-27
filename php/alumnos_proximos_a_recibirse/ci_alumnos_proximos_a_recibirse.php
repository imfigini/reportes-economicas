<?php
require_once ('MisConsultas.php');

class ci_alumnos_proximos_a_recibirse extends toba_ci
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

	function get_alumnos_con_porcentaje($porcentaje)
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT 	R.*, 
						S.nombre AS sede,
						DECODE (solo_falta_tesis, '1', 'Si', '-1', 'No', 0, 'Nada') AS falta_tesis
					FROM rep_porcentaje_carrera R
					JOIN sga_alumnos A ON (A.legajo = R.legajo AND A.carrera = R.carrera)
					JOIN sga_sedes S ON (S.sede = A.sede) ";
		if (trim($porcentaje) != "")
		{
			$sql = $sql . "WHERE porcentaje >= $porcentaje" ;
		}
		$sql = $sql . " ORDER BY alumno";
		//ei_arbol($sql);
		$alumnos = $db->query($sql)->fetchAll(); //PDO::FETCH_NUM
		return $alumnos;
	}

		
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			//ei_arbol($filtro);
			$porcentaje = $filtro['porcentaje'];
			$datos = $this->get_alumnos_con_porcentaje($porcentaje);
			$cuadro->set_datos($datos);
		} 
		else 
		{
			$cuadro->limpiar_columnas();
			//$cuadro->set_datos($this->dep('datos')->tabla('prueba')->get_listado());
		}
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
		$this->set_pantalla('pant_edicion');
	}

	//---- EVENTOS CI -------------------------------------------------------------------

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

	
	//-----------------------------------------------------------------------------------
	//---- Formulario ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__formulario__procesar()
	{
		$db = MisConsultas::getConexion();
		$sql = "EXECUTE PROCEDURE sp_rep_porcentaje_carrera()";
		$db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_ultima_actualizacion()
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
					FROM rep_fecha_actualiz_tablas
						WHERE tabla = 'rep_porcentaje_carrera'";
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];		
	}

}