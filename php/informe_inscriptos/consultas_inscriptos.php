<?php
require_once('MisConsultas.php');
class consultas_inscriptos
{
	//Actualiza la tabla rep_datos_inscriptos de un determinado año
	static function pre_procesar_inscriptos($anio) 
	{
            $db = MisConsultas::getConexionMini ($anio);
            $sqlText = "EXECUTE PROCEDURE sp_getDatosInscriptos($anio)";
            $db->query($sqlText);
	}
	
	//Retorna la cantidad total de inscriptos al curso de ingreso para un determinado año
	static function get_cantidad_total_inscriptos($anio) 
	{
		$db = MisConsultas::getConexionMini ($anio);
		$sql = "SELECT COUNT(DISTINCT nro_inscripcion) AS total_inscriptos
                            FROM rep_datos_inscriptos
                            WHERE periodo_inscripcio = '$anio'";
		$cant = $db->query($sql)->fetchAll(); //PDO::FETCH_ASSOC
		return $cant[0];
	}
	
	//Retorna la cantidad de inscriptos por carrera al curso de ingreso para un determinado año
	static function get_inscriptos_x_carrera($anio)
	{
		$db = MisConsultas::getConexionMini ($anio);
		$sql = "SELECT C.nombre AS carrera, COUNT(A.legajo) AS CANT_INSCRIPTOS
					FROM sga_alumnos A
					JOIN sga_carreras C ON (A.carrera = C.carrera)
					JOIN sga_carrera_aspira S ON (A.nro_inscripcion = S.nro_inscripcion AND A.carrera = S.carrera)
					WHERE	S.periodo_inscripcio = '$anio'
						AND A.carrera <> 211
					GROUP BY C.nombre
					ORDER BY 2 DESC";
		$inscr = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $inscr;		
	}
	
	//Retorna la cantidad de inscriptos por carrera y por localidad de colegio secundario al curso de ingreso para un determinado año
	static function get_inscriptos_x_carrera_x_localidad($anio)
	{
		$db = MisConsultas::getConexionMini ($anio);
		$sql = "SELECT C.nombre AS carrera, NVL(L.nombre, 'Sin definir') AS localidad, R.nombre AS provincia, COUNT(P.nro_inscripcion) AS cant_inscriptos
					FROM sga_alumnos A
					JOIN sga_carrera_aspira CA ON (A.nro_inscripcion = CA.nro_inscripcion AND A.carrera = CA.carrera)
					JOIN sga_carreras C ON (A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera)
					JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
					LEFT JOIN sga_coleg_sec S ON (P.colegio_secundario = S.colegio)
					LEFT JOIN mug_localidades L ON (S.localidad = L.localidad)
					LEFT JOIN mug_dptos_partidos D ON (L.dpto_partido = D.dpto_partido)
					LEFT JOIN mug_provincias R ON (D.provincia = R.provincia)
                                WHERE CA.periodo_inscripcio = '$anio'
                                     AND A.carrera NOT IN (211, 290)
				GROUP BY 1,2,3
				ORDER BY 4 DESC";
		$inscr = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $inscr;		
	}
	
	//Retorna la cantidad de inscriptos por localidad de colegio secundario al curso de ingreso para un determinado año (sin importar carrera)
	static function get_inscriptos_x_localidad($anio)
	{
		$db = MisConsultas::getConexionMini ($anio);
		//Excluye las carreras 211 y 290
		$sql = "SELECT NVL(L.nombre, '') AS localidad, R.nombre AS provincia, COUNT(P.nro_inscripcion) AS cant_inscriptos
					FROM sga_alumnos A
					JOIN sga_carrera_aspira CA ON (A.nro_inscripcion = CA.nro_inscripcion AND A.carrera = CA.carrera)
                                        JOIN sga_carreras C ON (A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera)
					JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
					LEFT JOIN sga_coleg_sec S ON (P.colegio_secundario = S.colegio)
					LEFT JOIN mug_localidades L ON (S.localidad = L.localidad)
					LEFT JOIN mug_dptos_partidos D ON (L.dpto_partido = D.dpto_partido)
					LEFT JOIN mug_provincias R ON (D.provincia = R.provincia)
                                WHERE CA.periodo_inscripcio = '$anio'
                                    AND A.carrera NOT IN (211, 290)
                            GROUP BY 1,2
                            ORDER BY 3 DESC";
		$inscr = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $inscr;		
	}

	//Retorna la cantidad de inscriptos por carrera y por colegio de Tandil al curso de ingreso para un determinado año
	static function get_inscriptos_x_carrera_x_colegio_Tandil($anio)
	{
		$db = MisConsultas::getConexionMini ($anio);
		$sql = "SELECT C.nombre AS carrera, NVL(S.nombre, '') AS colegio, COUNT(P.nro_inscripcion) AS cant_inscriptos
					FROM sga_alumnos A
                                        JOIN sga_carrera_aspira CA ON (A.nro_inscripcion = CA.nro_inscripcion AND A.carrera = CA.carrera)
					JOIN sga_carreras C ON (C.unidad_academica = A.unidad_academica AND C.carrera = A.carrera)
					JOIN sga_personas P ON (P.unidad_academica = A.unidad_academica AND P.nro_inscripcion = A.nro_inscripcion)
					LEFT JOIN sga_coleg_sec S ON (P.colegio_secundario = S.colegio)
                                WHERE S.localidad IN (16533, 16535)
                                    AND CA.periodo_inscripcio = '$anio'
                                    AND A.carrera NOT IN (211, 290)                                    
                            GROUP BY 1,2
                            ORDER BY 3 DESC";
		$inscr = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $inscr;		
	}

	//Retorna la cantidad de inscriptos por colegio de Tandil al curso de ingreso para un determinado año
	static function get_inscriptos_x_colegio_Tandil($anio)
	{
		$db = MisConsultas::getConexionMini ($anio);
		//Excluye las carreras 211 y 290
		$sql = "SELECT NVL(S.nombre, '') AS colegio, COUNT(P.nro_inscripcion) AS cant_inscriptos
					FROM sga_alumnos A
                                        JOIN sga_carrera_aspira CA ON (A.nro_inscripcion = CA.nro_inscripcion AND A.carrera = CA.carrera)
					JOIN sga_carreras C ON (C.unidad_academica = A.unidad_academica AND C.carrera = A.carrera)
					JOIN sga_personas P ON (P.unidad_academica = A.unidad_academica AND P.nro_inscripcion = A.nro_inscripcion)
					LEFT JOIN sga_coleg_sec S ON (P.colegio_secundario = S.colegio)
                                WHERE S.localidad IN (16533, 16535)
                                    AND CA.periodo_inscripcio = '$anio'
                                    AND A.carrera NOT IN (211, 290)  
                            GROUP BY 1
                            ORDER BY 2 DESC";
		$inscr = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $inscr;		
	}	
}

?>