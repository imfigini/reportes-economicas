<?php
require_once('MisConsultas.php');
class consultas_ingresantes
{
	//Retorna la cantidad total de ingresantes a la Facultad para un determinado año
	//Se consideran sólo ingresantes nuevos, se descartan alumnos que ya venían de otra carrera o de otra facultad o universidad. Sólo se consideran los que no tienen ninguna equivalencia. 
	static function get_cantidad_total_ingresantes($anio) 
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT COUNT (DISTINCT A.legajo) AS CANT_INGRESANTES
					FROM sga_carrera_aspira X, sga_periodo_insc I, sga_alumnos A
					WHERE X.periodo_inscripcio = I.periodo_inscripcio
						AND I.anio_academico = $anio
						AND X.unidad_academica = A.unidad_academica AND X.nro_inscripcion = A.nro_inscripcion AND X.carrera = A.carrera
						AND A.legajo||A.carrera NOT IN (
								SELECT V.legajo||V.carrera FROM vw_hist_academica V 
								WHERE V.forma_aprobacion <> 'Examen' AND V.forma_aprobacion <> 'Promoción'
									AND V.resultado = 'A'
							)
						AND A.nro_inscripcion||A.carrera NOT IN (
								SELECT V.legajo||V.carrera FROM sga_cursadas V 
								WHERE V.origen <> 'P' AND V.origen <> 'C'
									AND V.resultado = 'A'
							)
						AND A.carrera NOT IN (290, 211)
					ORDER BY 1 DESC";
		$cant = $db->query($sql)->fetchAll(); //PDO::FETCH_ASSOC
		return $cant[0];
	}
	
	//Retorna la cantidad de ingresantes por carrera para un determinado año
	//Se consideran sólo ingresantes nuevos, se descartan alumnos que ya venían de otra carrera o de otra facultad o universidad. Sólo se consideran los que no tienen ninguna equivalencia. 
	static function get_ingresantes_x_carrera($anio)
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT C.nombre AS carrera, COUNT (A.legajo) AS CANT_INGRESANTES
					FROM sga_carrera_aspira X, sga_periodo_insc I, sga_alumnos A, sga_carreras C
					WHERE X.periodo_inscripcio = I.periodo_inscripcio
						AND I.anio_academico = $anio
						AND X.unidad_academica = A.unidad_academica AND X.nro_inscripcion = A.nro_inscripcion AND X.carrera = A.carrera
						AND A.nro_inscripcion||A.carrera NOT IN (
								SELECT V.legajo||V.carrera FROM vw_hist_academica V 
								WHERE V.forma_aprobacion <> 'Examen' AND V.forma_aprobacion <> 'Promoción'
									AND V.resultado = 'A'
							)
						AND A.nro_inscripcion||A.carrera NOT IN (
								SELECT V.legajo||V.carrera FROM sga_cursadas V 
								WHERE V.origen <> 'P' AND V.origen <> 'C'
									AND V.resultado = 'A'
							)
						AND A.carrera = C.carrera
						AND A.carrera NOT IN (290, 211)
					GROUP BY 1
					ORDER BY 2 DESC";
                //ei_arbol($sql);
		$ingresantes = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $ingresantes;		
	}
	
	//Retorna la cantidad de ingresantes por carrera y por localidad, provincia de colegio secundario para un determinado año
	//Se consideran sólo ingresantes nuevos, se descartan alumnos que ya venían de otra carrera o de otra facultad o universidad. Sólo se consideran los que no tienen ninguna equivalencia. 
	static function get_ingresantes_x_carrera_x_localidad($anio)
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT C.nombre AS carrera, NVL(L.nombre, '') AS localidad, R.nombre AS provincia, COUNT (A.legajo) AS CANT_INGRESANTES
					FROM sga_carrera_aspira X
					JOIN sga_periodo_insc I ON (X.periodo_inscripcio = I.periodo_inscripcio AND I.anio_academico = $anio)
					JOIN sga_alumnos A ON (X.unidad_academica = A.unidad_academica AND X.nro_inscripcion = A.nro_inscripcion AND X.carrera = A.carrera)
					JOIN sga_carreras C ON (C.unidad_academica = A.unidad_academica AND C.carrera = A.carrera AND C.carrera NOT IN (290, 211))
					JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
					LEFT JOIN sga_coleg_sec S ON (P.colegio_secundario = S.colegio)
					LEFT JOIN mug_localidades L ON (S.localidad = L.localidad)
					LEFT JOIN mug_dptos_partidos D ON (L.dpto_partido = D.dpto_partido)
					LEFT JOIN mug_provincias R ON (D.provincia = R.provincia)
					WHERE 	A.nro_inscripcion||A.carrera NOT IN (
								SELECT V.legajo||V.carrera FROM vw_hist_academica V 
								WHERE V.forma_aprobacion <> 'Examen' AND V.forma_aprobacion <> 'Promoción'
									AND V.resultado = 'A'
							)
						AND A.nro_inscripcion||A.carrera NOT IN (
								SELECT V.legajo||V.carrera FROM sga_cursadas V 
								WHERE V.origen <> 'P' AND V.origen <> 'C'
									AND V.resultado = 'A'
							)
					GROUP BY 1,2,3
					ORDER BY 4 DESC";
		$ingresantes = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $ingresantes;		
	}
	
	//Retorna la cantidad de ingresantes por localidad, provincia de colegio secundario para un determinado año (sin importar carrera)
	//Se consideran sólo ingresantes nuevos, se descartan alumnos que ya venían de otra carrera o de otra facultad o universidad. Sólo se consideran los que no tienen ninguna equivalencia. 
	static function get_ingrasantes_x_localidad($anio)
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT NVL(L.nombre, '') AS localidad, R.nombre AS provincia, COUNT (A.legajo) AS CANT_INGRESANTES
					FROM sga_carrera_aspira X
					JOIN sga_periodo_insc I ON (X.periodo_inscripcio = I.periodo_inscripcio AND I.anio_academico = $anio)
					JOIN sga_alumnos A ON (X.unidad_academica = A.unidad_academica AND X.nro_inscripcion = A.nro_inscripcion AND X.carrera = A.carrera)
					JOIN sga_carreras C ON (C.unidad_academica = A.unidad_academica AND C.carrera = A.carrera AND C.carrera NOT IN (290, 211))
					JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
					LEFT JOIN sga_coleg_sec S ON (P.colegio_secundario = S.colegio)
					LEFT JOIN mug_localidades L ON (S.localidad = L.localidad)
					LEFT JOIN mug_dptos_partidos D ON (L.dpto_partido = D.dpto_partido)
					LEFT JOIN mug_provincias R ON (D.provincia = R.provincia)
					WHERE 	A.nro_inscripcion||A.carrera NOT IN (
								SELECT V.legajo||V.carrera FROM vw_hist_academica V 
								WHERE V.forma_aprobacion <> 'Examen' AND V.forma_aprobacion <> 'Promoción'
									AND V.resultado = 'A'
							)
						AND A.nro_inscripcion||A.carrera NOT IN (
								SELECT V.legajo||V.carrera FROM sga_cursadas V 
								WHERE V.origen <> 'P' AND V.origen <> 'C'
									AND V.resultado = 'A'
							)
					GROUP BY 1,2
					ORDER BY 3 DESC";
		$ingresantes = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $ingresantes;		
	}
	
	//Retorna la cantidad de ingresantes por carrera y por colegio de Tandil para un determinado año
	static function get_ingresantes_x_carrera_x_colegio_Tandil($anio)
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT C.nombre AS carrera, S.nombre AS colegio, COUNT (A.legajo) AS CANT_INGRESANTES
					FROM sga_carrera_aspira X
					JOIN sga_periodo_insc I ON (X.periodo_inscripcio = I.periodo_inscripcio AND I.anio_academico = $anio)
					JOIN sga_alumnos A ON (X.unidad_academica = A.unidad_academica AND X.nro_inscripcion = A.nro_inscripcion AND X.carrera = A.carrera)
					JOIN sga_carreras C ON (C.unidad_academica = A.unidad_academica AND C.carrera = A.carrera AND C.carrera NOT IN (290, 211))
					JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
					LEFT JOIN sga_coleg_sec S ON (P.colegio_secundario = S.colegio)
					WHERE 	S.localidad IN (16533, 16535)
						AND A.nro_inscripcion||A.carrera NOT IN (
								SELECT V.legajo||V.carrera FROM vw_hist_academica V 
								WHERE V.forma_aprobacion <> 'Examen' AND V.forma_aprobacion <> 'Promoción'
									AND V.resultado = 'A'
							)
						AND A.nro_inscripcion||A.carrera NOT IN (
								SELECT V.legajo||V.carrera FROM sga_cursadas V 
								WHERE V.origen <> 'P' AND V.origen <> 'C'
									AND V.resultado = 'A'
							)
					GROUP BY 1, 2
					ORDER BY 3 DESC";
		$ingresantes = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $ingresantes;		
	}

	//Retorna la cantidad de ingresantes por colegio de Tandil para un determinado año (sin importar carrera)
	static function get_ingresantes_x_colegio_Tandil($anio)
	{
		$db = MisConsultas::getConexion();
		//Excluye carreras 290 y 211
		$sql = "SELECT S.nombre AS colegio, COUNT (A.legajo) AS CANT_INGRESANTES
					FROM sga_carrera_aspira X
					JOIN sga_periodo_insc I ON (X.periodo_inscripcio = I.periodo_inscripcio AND I.anio_academico = $anio)
					JOIN sga_alumnos A ON (X.unidad_academica = A.unidad_academica AND X.nro_inscripcion = A.nro_inscripcion AND X.carrera = A.carrera)
					JOIN sga_carreras C ON (C.unidad_academica = A.unidad_academica AND C.carrera = A.carrera AND C.carrera NOT IN (290, 211))
					JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
					LEFT JOIN sga_coleg_sec S ON (P.colegio_secundario = S.colegio)
					WHERE 	S.localidad IN (16533, 16535)
						AND A.nro_inscripcion||A.carrera NOT IN (
								SELECT V.legajo||V.carrera FROM vw_hist_academica V 
								WHERE V.forma_aprobacion <> 'Examen' AND V.forma_aprobacion <> 'Promoción'
									AND V.resultado = 'A'
							)
						AND A.nro_inscripcion||A.carrera NOT IN (
								SELECT V.legajo||V.carrera FROM sga_cursadas V 
								WHERE V.origen <> 'P' AND V.origen <> 'C'
									AND V.resultado = 'A'
							)
					GROUP BY 1
					ORDER BY 2 DESC";
		$ingresantes = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $ingresantes;		
	}
	
}

?>