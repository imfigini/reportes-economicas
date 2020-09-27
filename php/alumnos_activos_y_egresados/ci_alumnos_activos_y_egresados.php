<?php
require_once 'MisConsultas.php';

class ci_alumnos_activos_y_egresados extends toba_ci
{
	//---- Cuadro -----------------------------------------------------------------------

	function get_alumnos_activos_ya_egresados()
	{
		$sql = "SELECT 	DISTINCT C.nombre || ' (' || C.carrera || ')' AS carrera_actual, 
					A.legajo, 
					P.apellido || ', ' || P.nombres AS alumno,
					C2.nombre || ' (' || C2.carrera || ')' AS carrera_egresado
						FROM sga_alumnos A
						JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
						JOIN sga_carreras C ON (A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera)
						JOIN sga_titulos_otorg T ON (A.unidad_academica = T.unidad_academica AND A.nro_inscripcion = T.nro_inscripcion)
						JOIN sga_carreras C2 ON (T.unidad_academica = C2.unidad_academica AND T.carrera = C2.carrera)
							WHERE A.regular = 'S' AND A.calidad = 'A'
								AND A.legajo IN (SELECT legajo FROM sga_alumnos WHERE calidad = 'E')
					ORDER BY 1,3";
		
		$db = MisConsultas::getConexion ();
		$datos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $datos;
	}
	
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = self::get_alumnos_activos_ya_egresados();
		$cuadro->set_datos($datos);
	}

}

?>