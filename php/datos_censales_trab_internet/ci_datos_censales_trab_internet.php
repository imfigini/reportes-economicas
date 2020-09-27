<?php
require_once 'MisConsultas.php';

class ci_datos_censales_trab_internet extends toba_ci
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
		if (isset($this->s__datos_filtro)) {
			$anio = $this->s__datos_filtro['ANIO_ACADEMICO'];
			$datos = $this->get_datos($anio);
			$cuadro->set_datos($datos);
		} else {
			$cuadro->limpiar_columnas();
		}
	}

	//Retorna los datos de los alumnos que ingresaron o se rematricularon un determinado año
	function get_datos($anio)
	{
		$sql = "SELECT DISTINCT
					MIN(YEAR(A.fecha_ingreso)) AS anio_ingreso,
					P.nro_inscripcion,
					P.apellido || ', ' || P.nombres as alumno,
					S.nombre AS sede,
					NVL(NVL(G.e_mail, D.e_mail),'') as e_mail,
					D2.fecha_relevamiento, 
					CASE
						WHEN D1.existe_trab_alum = '3' THEN 'No'
							WHEN D1.existe_trab_alum = '2' THEN 'No pero busca'
							WHEN D1.existe_trab_alum = '1' THEN 'Si'
						ELSE '-'
					END AS existe_trab_alum,
					CASE
						WHEN D2.alu_trab_ocup = '1' THEN 'Permanente'
							WHEN D2.alu_trab_ocup = '2' THEN 'Temporaria'
						ELSE '-'
					END AS alu_trab_ocup,
					CASE
						WHEN D.hora_sem_trab_alum = '1' THEN 'Hasta 10 h'
							WHEN D.hora_sem_trab_alum = '2' THEN 'Mas de 10 y hasta 20 h'
							WHEN D.hora_sem_trab_alum = '3' THEN 'Mas de 20 y menos de 35 h'
							WHEN D.hora_sem_trab_alum = '4' THEN '35 o mas h'
						ELSE '-'
					END AS hora_sem_trab_alum,
					D1.remuneracion,
					D2.alu_cos_est_ap_fam, 
					D2.alu_cos_est_trab, 
					D2.alu_cos_est_beca, 
					D2.alu_cos_est_plsoc, 
					D2.alu_cos_est_otra, 
					D1.obra_social_alu,
					D2.alu_tec_pc_casa, 
					D2.alu_tec_pc_trab, 
					D2.alu_tec_pc_univ, 
					D2.alu_tec_pc_otro, 
					D2.alu_tec_int_casa, 
					D2.alu_tec_int_trab, 
					D2.alu_tec_int_univ, 
					D2.alu_tec_int_cyber, 
					D2.alu_tec_int_otro
				FROM sga_datos_cen_aux2 D2
				JOIN sga_alumnos A ON (A.unidad_academica = D2.unidad_academica AND A.nro_inscripcion = D2.nro_inscripcion AND A.regular = 'S' AND A.calidad = 'A')
				JOIN sga_personas P ON (P.unidad_academica = D2.unidad_academica AND P.nro_inscripcion = D2.nro_inscripcion)
				JOIN sga_datos_censales D ON (D.unidad_academica = D2.unidad_academica AND D.nro_inscripcion = D2.nro_inscripcion)
				JOIN sga_datos_cen_aux D1 ON (D1.unidad_academica = D2.unidad_academica AND D1.nro_inscripcion = D2.nro_inscripcion)
				JOIN sga_sedes S ON (S.sede = A.sede)
				LEFT JOIN gda_anun_conf_pers G ON (G.unidad_academica = D2.unidad_academica AND G.nro_inscripcion = D2.nro_inscripcion)
				WHERE D2.fecha_relevamiento = (
						SELECT MAX(fecha_relevamiento) 
							FROM sga_datos_cen_aux2 X2
							WHERE X2.unidad_academica = D2.unidad_academica 
							AND X2.nro_inscripcion = D2.nro_inscripcion 
						)
					AND D.fecha_relevamiento = (
						SELECT MAX(fecha_relevamiento) 
							FROM sga_datos_censales X
							WHERE X.unidad_academica = D.unidad_academica 
							AND X.nro_inscripcion = D.nro_inscripcion 
						)
					AND D1.fecha_relevamiento = (
						SELECT MAX(fecha_relevamiento) 
							FROM sga_datos_cen_aux X1
							WHERE X1.unidad_academica = D1.unidad_academica 
							AND X1.nro_inscripcion = D1.nro_inscripcion 
						)
					AND (
						A.legajo IN (
							SELECT legajo 
							FROM sga_reinscripcion R 
							WHERE R.unidad_academica = A.unidad_academica 
								AND R.carrera = A.carrera 
								AND anio_academico = $anio
							)
						OR A.nro_inscripcion IN (
							SELECT nro_inscripcion 
							FROM sga_carrera_aspira ASP
							JOIN sga_periodo_insc PI ON (ASP.periodo_inscripcio = PI.periodo_inscripcio)
							WHERE ASP.unidad_academica = A.unidad_academica 
								AND ASP.carrera = A.carrera 
								AND PI.anio_academico = $anio
						)
					)
				GROUP BY 2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25
				ORDER BY 1 DESC, 3 ASC";

		$db = MisConsultas::getConexion();
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

}

?>