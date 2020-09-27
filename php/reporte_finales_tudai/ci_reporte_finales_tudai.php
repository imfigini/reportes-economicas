<?php
require_once('MisConsultas.php');

class ci_reporte_finales_tudai extends toba_ci
{
	function get_finales_tudai()
	{
		$sql = "SELECT 	legajo, 
						YEAR(fecha_ingreso) AS anio_ingreso,
						NVL(nombre_materia, '') AS materia,
						NVL(anio_de_cursada, '') AS anio_de_cursada,
						CASE	WHEN resultado = 'P' THEN 'Promovido'
								WHEN resultado = 'A' THEN 'Aprobado'
								WHEN resultado = 'R' THEN 'Reprobado'
								WHEN resultado = 'U' THEN 'Ausente'
								ELSE ''
							END AS resultado, 
						NVL(fecha, '') AS fecha,
						NVL(forma_aprobacion, '') AS forma_aprobacion
					FROM 	rep_tudai_finales";
		
		$db = MisConsultas::getConexion ();
		$cursadas = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $cursadas;
	}

	//-----------------------------------------------------------------------------------
	//---- Formulario ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function get_ultima_actualizacion()
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
					FROM rep_fecha_actualiz_tablas
						WHERE tabla = 'rep_tudai_finales'";
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];		
	}

	//-----------------------------------------------------------------------------------
	//---- tudai_cursadas_procesar ------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__tudai_finales_procesar__procesar($datos)
	{
		$db = MisConsultas::getConexion();
		$sql = "EXECUTE PROCEDURE 'dba'.sp_rep_tudai_finales()";
		$result = $db->query($sql)->fetchAll(); //->fetchAll();
	}

	//-----------------------------------------------------------------------------------
	//---- cuadro -----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	//function conf__cuadro(Reportes_ei_cuadro $cuadro)
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		//ei_arbol($cuadro, 'conf__cuadro');
		$datos = $this->get_finales_tudai();
		//ei_arbol($datos, 'datos');
		$cuadro->set_datos($datos);
	}

}
?>