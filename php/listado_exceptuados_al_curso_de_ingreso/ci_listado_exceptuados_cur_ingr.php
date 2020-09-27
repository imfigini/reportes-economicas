<?php
require_once('MisConsultas.php');

class ci_listado_exceptuados_cur_ingr extends toba_ci
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
                $anio = $this->s__datos_filtro['ANIO_ACADEMICO'];
                $datos = $this->get_ingresantes_con_curso_ingr_exceptuado($anio);
                $cuadro->set_datos($datos);
            } 
            else 
            {
                $cuadro->limpiar_columnas();
            }
	}

        function get_ingresantes_con_curso_ingr_exceptuado($anio)
        {
            $db = MisConsultas::getConexion($anio);
            $sql = "SELECT  S.nombre AS sede, 
                            C.nombre_reducido AS carrera, 
                            P.apellido || ', '|| P.nombres AS ingresante, 
                            P.nro_documento, 
                            NVL(ME.descripcion, CA.observaciones) AS motivo,
                            MIN(A.fecha_ingreso) AS primer_fecha_ingreso
                        FROM sga_carrera_aspira CA
                            JOIN sga_personas P ON (P.unidad_academica = CA.unidad_academica AND P.nro_inscripcion = CA.nro_inscripcion)
                            JOIN sga_carreras C ON (C.carrera = CA.carrera)
                            JOIN sga_sedes S ON (S.sede = CA.sede)
                            LEFT JOIN sga_mot_exc_curing ME ON (ME.motivo_excep_curin = CA.motivo_excep_curso)
                            LEFT JOIN sga_alumnos A ON (A.unidad_academica = CA.unidad_academica AND A.nro_inscripcion = CA.nro_inscripcion)
                        WHERE 
                            CA.periodo_inscripcio LIKE '%$anio' AND 
                            CA.forma_cumpli_curso = 'E'
                    GROUP BY 1,2,3,4,5
                    ORDER BY 1,2,3,4 ";
            return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }
	
}

?>