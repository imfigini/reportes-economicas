<?php
require_once('MisConsultas.php');

class ci_requisito3 extends toba_ci
{
	protected $s__datos_filtro;


	//---- Filtro -----------------------------------------------------------------------

	/** Retorna el listado de materias pertenecientes a algún plan activo vigente **/
	function get_dptos() 
	{
		$db = MisConsultas::getConexion ();
		
		$sqlText = "SELECT 	departamento AS dpto, nombre AS dpto_nombre
						FROM sga_departamentos
						WHERE nombre <> 'Decanato'
					ORDER BY nombre";

		$materias = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);
		return $materias;
	}	
		
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

	function get_alumnos_con_50porc_carreras_dpto($filtro)
	{
		$dpto = $filtro['DPTO'];
		
		$db = MisConsultas::getConexion ();
		$sql = "SELECT carrera, nombre FROM sga_carreras WHERE departamento = '$dpto' AND estado = 'A'";
		$carreras = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		$resultado = array();
		
		foreach ($carreras AS $carrera)
		{
			$carrera = $carrera['CARRERA'];
			$sql = "SELECT R.legajo, R.alumno, R.nombre_carrera, R.porcentaje, D.e_mail
					FROM rep_porcentaje_carrera R, vw_datos_censales_actuales D
						WHERE R.porcentaje >= 50
							AND R.legajo = D.nro_inscripcion
							AND R.carrera = $carrera
							AND R.legajo NOT IN (SELECT legajo FROM sga_alumnos WHERE calidad = 'E')";
		
			$alumnos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			$resultado = array_merge($resultado, $alumnos);
		}
		
		$max = count($resultado);
		for ($i=0; $i<$max; $i++)
		{
			$num_aleatorio = rand(1,5000);
			$resultado[$i]['NUM_ALEAT'] = $num_aleatorio;
		}
//		ei_arbol($resultado);
		return $resultado;
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$datos = $this->get_alumnos_con_50porc_carreras_dpto($this->s__datos_filtro);
			$cuadro->set_datos($datos);
		} 
		else 
		{
			$cuadro->limpiar_columnas();
		}
	}


	//-----------------------------------------------------------------------------------
	//---- Formulario ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__formulario__procesar()
	{
		$db = MisConsultas::getConexion();
		$sql = "EXECUTE PROCEDURE sp_alumnos_prox_recibirse()";
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

?>