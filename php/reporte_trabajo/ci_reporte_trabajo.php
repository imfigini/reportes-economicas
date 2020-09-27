<?php
require_once('MisConsultas.php');

class ci_reporte_trabajo extends toba_ci
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
                    $anio = $this->s__datos_filtro['anio'];
                    $datos = self::get_datos($anio);
		} 
                else 
                {
                    $datos = self::get_datos(null);
		}
                $cuadro->set_datos($datos);
	}


        //------- Funcionalidad extra --------------------------------------------------------
        
        function get_anios ()
        {
            $sql = "SELECT DISTINCT YEAR(fecha_ingreso) AS anio
                        FROM sga_alumnos 
                            WHERE carrera = 213
                        ORDER BY 1 DESC";
            
            $db = MisConsultas::getConexion ();
            return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }
        
        function get_datos($anio = null)
        {
            // Dato actual
            $sql = "SELECT A.nro_inscripcion, 
                            YEAR(A.fecha_ingreso) AS anio_ingreso,
                            NVL (D.fecha_relevamiento, D2.fecha_relevamiento) AS fecha_relevamiento,
                            D.hora_sem_trab_alum,
                            CASE
                                WHEN D.rel_trab_carrera = '1' THEN 'Total'
                                WHEN D.rel_trab_carrera = '2' THEN 'Parcial'
                                WHEN D.rel_trab_carrera = '3' THEN 'Sin relación'
                                ELSE ''
                            END AS rel_trab_carrera,
                            CASE
                                WHEN D2.alu_trab_hace = '1' THEN 'Patrón (tiene empleados)'
                                WHEN D2.alu_trab_hace = '2' THEN 'Cuenta propia'
                                WHEN D2.alu_trab_hace = '3' THEN 'Empleado'
                                WHEN D2.alu_trab_hace = '4' THEN 'Pasante'
                                ELSE ''
                            END AS alu_trab_hace,
                            CASE
                                WHEN D2.alu_trab_fami = 'S' THEN 'Si' 
                                WHEN D2.alu_trab_fami = 'N' THEN 'No'
                                ELSE ''
                            END AS alu_trab_fami,
                            CASE 
                                WHEN D2.alu_trab_ocup = '1' THEN 'Permanente'
                                WHEN D2.alu_trab_ocup = '2' THEN 'Temporaria'
                                ELSE ''
                            END AS alu_trab_ocup,
                            D2.alu_trab_tarea
                    FROM sga_alumnos A
                            JOIN sga_datos_censales D ON (D.unidad_academica = A.unidad_academica AND D.nro_inscripcion = A.nro_inscripcion AND A.carrera = 213)
                            JOIN sga_datos_cen_aux2 D2 ON (D2.unidad_academica = A.unidad_academica AND D2.nro_inscripcion = A.nro_inscripcion AND A.carrera = 213)
                            WHERE A.carrera = 213
                            AND A.regular = 'S'
                            AND A.calidad = 'A'
                            AND D.fecha_relevamiento = (
                                          SELECT MAX(x0.fecha_relevamiento) 
                                            FROM sga_datos_censales x0
                                            WHERE ((x0.unidad_academica = D.unidad_academica ) AND (x0.nro_inscripcion = D.nro_inscripcion ) ) 	
                                    )
                            AND D2.fecha_relevamiento = (
                                          SELECT MAX(x1.fecha_relevamiento) 
                                            FROM sga_datos_cen_aux2 x1 
                                            WHERE ((x1.unidad_academica = D2.unidad_academica ) AND (x1.nro_inscripcion = D2.nro_inscripcion ) ) 	
                                    ) ";
            if (isset($anio))
            {
                $sql .= " AND YEAR(A.fecha_ingreso) = $anio ";
            }
            
            //Primer dato relevado
            $sql .= " UNION SELECT A.nro_inscripcion, 
                            YEAR(A.fecha_ingreso) AS anio_ingreso,
                            NVL (D.fecha_relevamiento, D2.fecha_relevamiento) AS fecha_relevamiento,
                            D.hora_sem_trab_alum,
                            CASE
                                WHEN D.rel_trab_carrera = '1' THEN 'Total'
                                WHEN D.rel_trab_carrera = '2' THEN 'Parcial'
                                WHEN D.rel_trab_carrera = '3' THEN 'Sin relación'
                                ELSE ''
                            END AS rel_trab_carrera,
                            CASE
                                WHEN D2.alu_trab_hace = '1' THEN 'Patrón (tiene empleados)'
                                WHEN D2.alu_trab_hace = '2' THEN 'Cuenta propia'
                                WHEN D2.alu_trab_hace = '3' THEN 'Empleado'
                                WHEN D2.alu_trab_hace = '4' THEN 'Pasante'
                                ELSE ''
                            END AS alu_trab_hace,
                            CASE
                                WHEN D2.alu_trab_fami = 'S' THEN 'Si' 
                                WHEN D2.alu_trab_fami = 'N' THEN 'No'
                                ELSE ''
                            END AS alu_trab_fami,
                            CASE 
                                WHEN D2.alu_trab_ocup = '1' THEN 'Permanente'
                                WHEN D2.alu_trab_ocup = '2' THEN 'Temporaria'
                                ELSE ''
                            END AS alu_trab_ocup,
                            D2.alu_trab_tarea
                    FROM sga_alumnos A
                            JOIN sga_datos_censales D ON (D.unidad_academica = A.unidad_academica AND D.nro_inscripcion = A.nro_inscripcion AND A.carrera = 213)
                            JOIN sga_datos_cen_aux2 D2 ON (D2.unidad_academica = A.unidad_academica AND D2.nro_inscripcion = A.nro_inscripcion AND A.carrera = 213)
                            WHERE A.carrera = 213
                            AND A.regular = 'S'
                            AND A.calidad = 'A'
                            AND D.fecha_relevamiento = (
                                          SELECT MIN(x0.fecha_relevamiento) 
                                            FROM sga_datos_censales x0
                                            WHERE ((x0.unidad_academica = D.unidad_academica ) AND (x0.nro_inscripcion = D.nro_inscripcion ) ) 	
                                    )
                            AND D2.fecha_relevamiento = (
                                          SELECT MIN(x1.fecha_relevamiento) 
                                            FROM sga_datos_cen_aux2 x1 
                                            WHERE ((x1.unidad_academica = D2.unidad_academica ) AND (x1.nro_inscripcion = D2.nro_inscripcion ) ) 	
                                    ) ";
            if (isset($anio))
            {
                $sql .= " AND YEAR(A.fecha_ingreso) = $anio ";
            }
            
            $db = MisConsultas::getConexion ();
            return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }
}
