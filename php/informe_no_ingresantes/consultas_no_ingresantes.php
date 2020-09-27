<?php
require_once('MisConsultas.php');
class consultas_no_ingresantes
{
    static function condicion_antes_2018()
    {
        return " A.legajo NOT IN (
                        SELECT vw_hist_academica.legajo FROM vw_hist_academica 
                        WHERE vw_hist_academica.resultado = 'A'
                        AND vw_hist_academica.carrera = A.carrera
                ) 
                AND A.legajo IN (
                        SELECT vw_hist_academica.legajo FROM vw_hist_academica 
                        WHERE vw_hist_academica.resultado = 'R'
                        AND vw_hist_academica.carrera = A.carrera
                ) 
                AND A.carrera NOT IN (290, 211) ";
    }
    
    static function condicion_desde_2018()
    {
        return " A.legajo NOT IN (
                        SELECT legajo FROM sga_alumnos A2
                            WHERE A2.carrera = A.carrera 
                                AND A2.legajo IN (
                                            SELECT legajo FROM vw_hist_academica WHERE materia = '001' AND resultado = 'A' AND carrera = A.carrera
                                    )
                                AND A2.legajo IN (
                                            SELECT legajo FROM vw_hist_academica WHERE materia = '002' AND resultado = 'A' AND carrera = A.carrera
                                    )
                                AND A2.legajo IN (
                                                SELECT legajo FROM vw_hist_academica WHERE materia = '003' AND resultado = 'A' AND carrera = A.carrera
                                    )
                        )
                 AND A.legajo IN (
                        SELECT legajo FROM sga_alumnos A2
                            WHERE A2.carrera = A.carrera 
                                AND (
                                    A2.legajo IN (
                                                SELECT legajo FROM vw_hist_academica WHERE materia = '001' AND resultado = 'R' AND carrera = A.carrera
                                        )
                                    OR A2.legajo IN (
                                                SELECT legajo FROM vw_hist_academica WHERE materia = '002' AND resultado = 'R' AND carrera = A.carrera
                                        )
                                    OR A2.legajo IN (
                                                    SELECT legajo FROM vw_hist_academica WHERE materia = '003' AND resultado = 'R' AND carrera = A.carrera
                                        )
                                )
                        ) ";
    }
    
    //Retorna la cantidad total de inscriptos al curso de ingreso que no ingresqaron y que reprobaron al menos una instancia del mismo
    static function get_cantidad_total_no_ingresantes($anio) 
    {
            $db = MisConsultas::getConexionMini($anio);
            if ($anio < 2018)
            {
                $condicion = self::condicion_antes_2018();
            }
            else 
            {
                $condicion = self::condicion_desde_2018();
            }
            $sql = "SELECT COUNT (DISTINCT A.legajo) AS CANT_NO_INGRESANTES
                        FROM sga_alumnos A
                        JOIN sga_carrera_aspira S ON (A.unidad_academica = S.unidad_academica AND A.nro_inscripcion = S.nro_inscripcion AND A.carrera = S.carrera)
                        WHERE S.periodo_inscripcio = '$anio'
                            AND $condicion";

            $cant = $db->query($sql)->fetchAll(); //PDO::FETCH_ASSOC
            return $cant[0];
    }

    //Retorna la cantidad de de inscriptos al curso de ingreso por carrera que no ingresaron y que reprobaron al menos una instancia del mismo
    static function get_no_ingresantes_x_carrera($anio)
    {
            $db = MisConsultas::getConexionMini($anio);
            if ($anio < 2018)
            {
                $condicion = self::condicion_antes_2018();
            }
            else 
            {
                $condicion = self::condicion_desde_2018();
            }           
            $sql = "SELECT C.nombre AS carrera, COUNT (A.legajo) AS CANT_NO_INGRESANTES
                            FROM sga_alumnos A
                            JOIN sga_carreras C ON (A.carrera = C.carrera)
                            JOIN sga_carrera_aspira S ON (A.unidad_academica = S.unidad_academica AND A.nro_inscripcion = S.nro_inscripcion AND A.carrera = S.carrera)
                            WHERE S.periodo_inscripcio = '$anio'
                                AND $condicion
                    GROUP BY 1
                    ORDER BY 2 DESC";
            $no_ingresantes = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $no_ingresantes;		
    }

    //Retorna la cantidad de inscriptos al curso de ingreso que no ingresaron y que reprobaron al menos una instancia, por carrera y por localidad para un determinado año
    static function get_no_ingresantes_x_carrera_x_localidad($anio)
    {
            $db = MisConsultas::getConexionMini($anio);
            if ($anio < 2018)
            {
                $condicion = self::condicion_antes_2018();
            }
            else 
            {
                $condicion = self::condicion_desde_2018();
            }           
            $sql = "SELECT C.nombre AS carrera, NVL(L.nombre, '') AS localidad, R.nombre AS provincia, COUNT (A.legajo) AS CANT_NO_INGRESANTES
                        FROM sga_alumnos A
                        JOIN sga_carreras C ON (A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera)
                        JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
                        JOIN sga_carrera_aspira CA ON (A.unidad_academica = CA.unidad_academica AND A.nro_inscripcion = CA.nro_inscripcion AND A.carrera = CA.carrera)
                        LEFT JOIN sga_coleg_sec S ON (P.colegio_secundario = S.colegio)
                        LEFT JOIN mug_localidades L ON (S.localidad = L.localidad)
                        LEFT JOIN mug_dptos_partidos D ON (L.dpto_partido = D.dpto_partido)
                        LEFT JOIN mug_provincias R ON (D.provincia = R.provincia)
                        WHERE CA.periodo_inscripcio = '$anio'
                              AND $condicion
                    GROUP BY 1,2,3
                    ORDER BY 4 DESC";
            $no_ingresantes = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $no_ingresantes;		
    }

    //Retorna la cantidad de inscriptos al curso de ingreso, que no ingresaron y que reprobaron al menos una instancia 
    //por localidad, provincia de colegio secundario para un determinado año (sin importar carrera)
    static function get_no_ingrasantes_x_localidad($anio)
    {
            $db = MisConsultas::getConexionMini ($anio);
            if ($anio < 2018)
            {
                $condicion = self::condicion_antes_2018();
            }
            else 
            {
                $condicion = self::condicion_desde_2018();
            }           

            $sql = "SELECT NVL(L.nombre, '') AS localidad, R.nombre AS provincia, COUNT(P.nro_inscripcion) AS CANT_NO_INGRESANTES
                        FROM sga_alumnos A
                        JOIN sga_carrera_aspira CA ON (A.unidad_academica = CA.unidad_academica AND A.nro_inscripcion = CA.nro_inscripcion AND A.carrera = CA.carrera)
                        JOIN sga_carreras C ON (A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera)
                        JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
                        LEFT JOIN sga_coleg_sec S ON (P.colegio_secundario = S.colegio)
                        LEFT JOIN mug_localidades L ON (S.localidad = L.localidad)
                        LEFT JOIN mug_dptos_partidos D ON (L.dpto_partido = D.dpto_partido)
                        LEFT JOIN mug_provincias R ON (D.provincia = R.provincia)
                        WHERE CA.periodo_inscripcio = '$anio'
                              AND $condicion
                    GROUP BY 1,2
                    ORDER BY 3 DESC";
            $inscr = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $inscr;		
    }

    //Retorna la cantidad de inscriptos al curso de ingreso que no ingresaron y que reprobaron al menos una instancia, por carrera y por colegio de Tandil para un determinado año
    static function get_no_ingresantes_x_carrera_x_colegio_Tandil($anio)
    {
            $db = MisConsultas::getConexionMini($anio);
            if ($anio < 2018)
            {
                $condicion = self::condicion_antes_2018();
            }
            else 
            {
                $condicion = self::condicion_desde_2018();
            }           
            $sql = "SELECT C.nombre AS carrera, S.nombre AS colegio, COUNT (A.legajo) AS CANT_NO_INGRESANTES
                        FROM sga_alumnos A
                        JOIN sga_carreras C ON (A.unidad_academica = C.unidad_academica AND A.carrera = C.carrera)
                        JOIN sga_carrera_aspira CA ON (A.unidad_academica = CA.unidad_academica AND A.nro_inscripcion = CA.nro_inscripcion AND A.carrera = CA.carrera)
                        JOIN sga_personas P ON (A.unidad_academica = P.unidad_academica AND A.nro_inscripcion = P.nro_inscripcion)
                        LEFT JOIN sga_coleg_sec S ON (P.colegio_secundario = S.colegio)
                        WHERE CA.periodo_inscripcio = '$anio'
                            AND S.localidad IN (16533, 16535)
                            AND $condicion
                    GROUP BY 1,2
                    ORDER BY 3 DESC";
            $no_ingresantes = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $no_ingresantes;
    }

    //Retorna la cantidad de inscriptos por colegio de Tandil al curso de ingreso que NO ingresaron y reprobaron al menos una instancia
    static function get_no_ingresantes_x_colegio_Tandil($anio)
    {
            $db = MisConsultas::getConexionMini ($anio);
            if ($anio < 2018)
            {
                $condicion = self::condicion_antes_2018();
            }
            else 
            {
                $condicion = self::condicion_desde_2018();
            }           

            $sql = "SELECT NVL(S.nombre, '') AS colegio, COUNT(P.nro_inscripcion) AS CANT_NO_INGRESANTES
                        FROM sga_alumnos A
                        JOIN sga_carreras C ON (C.unidad_academica = A.unidad_academica AND C.carrera = A.carrera)
                        JOIN sga_carrera_aspira CA ON (A.unidad_academica = CA.unidad_academica AND A.nro_inscripcion = CA.nro_inscripcion AND A.carrera = CA.carrera)
                        JOIN sga_personas P ON (P.unidad_academica = A.unidad_academica AND P.nro_inscripcion = A.nro_inscripcion)
                        LEFT JOIN sga_coleg_sec S ON (P.colegio_secundario = S.colegio)
                                WHERE CA.periodo_inscripcio = '$anio'
                                      AND $condicion
                    GROUP BY 1
                    ORDER BY 2 DESC";
            $inscr = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $inscr;		
    }	

    //Retorna la cantidad de incriptos al curso de ingreso que no ingresaron y reprobaron al menos una instancia y la cantidad de intentos que hicieron
    static function get_cant_veces_no_aprobaron($anio)
    {
            
        if ($anio < 2018)
        {
            $db = MisConsultas::getConexionMini($anio);
            $condicion = self::condicion_antes_2018();

            $sql = "SELECT A.legajo AS legajo, A.carrera AS carrera, COUNT(V.resultado) AS cant_veces
                        FROM sga_alumnos A
                        JOIN sga_carrera_aspira CA ON (A.unidad_academica = CA.unidad_academica AND A.nro_inscripcion = CA.nro_inscripcion AND A.carrera = CA.carrera)
                        LEFT JOIN vw_hist_academica V ON (A.unidad_academica = V.unidad_academica AND A.legajo = V.legajo AND A.carrera = V.carrera)
                        WHERE CA.periodo_inscripcio = '$anio'
                            AND $condicion
                    GROUP BY 1,2";
            $cant_veces = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            $max = count($cant_veces);
            $result = array(	array('INTENTOS'=>1, 'CANT_NO_INGRESANTES'=>0), 
                                                    array('INTENTOS'=>2, 'CANT_NO_INGRESANTES'=>0), 
                                                    array('INTENTOS'=>3, 'CANT_NO_INGRESANTES'=>0), 
                                                    array('INTENTOS'=>4, 'CANT_NO_INGRESANTES'=>0), 
                                                    array('INTENTOS'=>5, 'CANT_NO_INGRESANTES'=>0)	);
            for ($i=0; $i<$max; $i++)
            {
                switch ($cant_veces[$i]['CANT_VECES'])
                {   case 1: $result[0]['CANT_NO_INGRESANTES']++;
                            break;
                    case 2: $result[1]['CANT_NO_INGRESANTES']++;
                            break;
                    case 3: $result[2]['CANT_NO_INGRESANTES']++;
                            break;
                    case 4: $result[3]['CANT_NO_INGRESANTES']++;
                            break;
                    case 5: $result[4]['CANT_NO_INGRESANTES']++;
                            break;
                }
            }
            return $result;
        }
        return array();
    }
	
}