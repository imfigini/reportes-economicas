<?php
require_once 'MisConsultas.php';

class ci_cantidad_aprob_desap_materia extends toba_ci
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
                $materia = $this->s__datos_filtro['materia'];
                $datos = $this->calcular_cantidad_aprobados_desaprobados($materia);
                $cuadro->set_datos($datos);
            } 
            else 
            {
                $cuadro->limpiar_columnas();
            }
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
	}

        function get_materias_tudai()
        {
            $sqlText = "SELECT DISTINCT A.materia, A.nombre_materia || ' (' || A.materia || ')' AS nombre_materia
                                            FROM sga_atrib_mat_plan A
                                            JOIN sga_planes P ON (A.unidad_academica = P.unidad_academica AND A.carrera = P.carrera AND A.plan = P.plan AND A.version = P.version_actual)
                                            WHERE A.carrera = 213
                                            ORDER BY 2";

            $db = MisConsultas::getConexion();
            $materias = $db->query($sqlText);
            return $materias;
        }
        
        function calcular_cantidad_aprobados_desaprobados($materia)
        {
            $sqlText = "SELECT 	C.anio_academico, 
                                C.periodo_lectivo, 
                                C.materia, 
                                S.nombre AS sede,
                                CASE
                                        WHEN V.resultado = 'A' THEN 'Aprobado'
                                        WHEN V.resultado = 'R' THEN 'Reprobado'
                                        WHEN V.resultado = 'U' THEN 'Ausente'
                                        WHEN V.resultado = 'P' THEN 'Promomoción'
                                END AS resultado, 
                                COUNT(*) AS cantidad
                        FROM sga_cursadas V, sga_comisiones C, sga_sedes S
                                WHERE V.carrera = 213
                                AND V.comision = C.comision
                                AND C.anio_academico >= YEAR(TODAY)-5
                                AND C.materia = '$materia'
                                AND C.sede = S.sede
                        GROUP BY C.anio_academico, C.periodo_lectivo, S.nombre, C.materia, V.resultado
                        ORDER BY C.anio_academico, C.periodo_lectivo, S.nombre, C.materia, 5";
            
            $db = MisConsultas::getConexion();
            $resultado = $db->query($sqlText)->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        }
        
}

?>