<?php
require_once('MisConsultas.php');

class ci_inscriptos_a_cursar_ingreso extends toba_ci
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
                $filtro = $this->s__datos_filtro;
                $anio = $filtro['ANIO_ACADEMICO'];
                $periodo = $filtro['PERIODO_LECTIVO'];
                $datos = self::get_inscriptos_cursar($anio, $periodo);
                $cuadro->set_datos($datos);
            } 
            else 
            {
                $cuadro->limpiar_columnas();
            }
	}

	//---- Funcionalidad ---------------------------------------------------------------------

	//Retorna los años académicas de la base de ingreso
	static function get_anios_academicos()
	{
            $sql = "SELECT anio_academico FROM sga_anio_academico ORDER BY anio_academico DESC";
            $db = MisConsultas::getConexionMini();
            return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}
        
        //Retorna los períodos lectivos del año académico correspondiente
	static function get_periodos_lectivos($anio)
	{
            $sql = "SELECT periodo_lectivo FROM sga_periodos_lect WHERE anio_academico = $anio";
            $db = MisConsultas::getConexionMini();
            return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}
        
        static function get_inscriptos_cursar($anio, $periodo)
        {
            $sql = "SELECT  P.apellido, 
                            P.nombres,
                            P.nro_documento,
                            G.e_mail,
                            S.nombre AS sede,
                            M.nombre AS materia_nombre,
                            I.fecha_inscripcion
                        FROM sga_insc_cursadas  I
                        JOIN sga_alumnos A ON (I.legajo = A.legajo AND I.carrera = A.carrera)
                        JOIN sga_personas P ON (P.nro_inscripcion = A.nro_inscripcion)
                        JOIN sga_sedes S ON (S.sede = A.sede)
                        JOIN sga_comisiones C ON (C.comision = I.comision)
                        JOIN sga_materias M ON (M.materia = C.materia)
                        LEFT JOIN gda_anun_conf_pers G ON (G.nro_inscripcion = A.nro_inscripcion)
                            WHERE C.anio_academico = $anio
                            AND C.periodo_lectivo = '$periodo'
                    ORDER BY 1,2";
            $db = MisConsultas::getConexionMini();
            return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }
}

?>