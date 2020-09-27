<?php
require_once('MisConsultas.php');

class ci_inscriptos_curso_online extends toba_ci
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
                $anio = $filtro['anio_academico'];
                $datos = self::get_datos_inscriptos_online($anio);
                $cuadro->set_datos($datos);
            } 
            else 
                $cuadro->limpiar_columnas();
	}

        //-------------------------------------------------------------------------------------
        //
        //Retorna los años académicas de la base de ingreso
	static function get_anios_academicos()
	{
            $sql = "SELECT anio_academico FROM sga_anio_academico ORDER BY anio_academico DESC";
		
            $db = MisConsultas::getConexionMini();
            $anios = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $anios;
	}
        
        static function get_datos_inscriptos_online($anio) 
	{
            $db = MisConsultas::getConexionMini();

            $sql = "SELECT DISTINCT S.nombre AS sede, 
                                    A.legajo, 
                                    A.carrera, 
                                    P.nro_documento, 
                                    P.apellido || ', ' || P.nombres AS inscripto, 
                                    D.e_mail,
                                    L1.nombre AS localidad_procedencia,
                                    D.calle_proc || ' ' || D.numero_proc AS direc_procedencia,
                                    COL.nombre AS colegio_secundario,
                                    L2.nombre AS localidad_colegio
                        FROM sga_periodos_lect PL
			JOIN sga_comisiones C ON (C.anio_academico = PL.anio_academico AND C.periodo_lectivo = PL.periodo_lectivo)
			JOIN sga_insc_cursadas I ON (I.comision = C.comision)
			JOIN sga_alumnos A ON (A.legajo = I.legajo AND A.carrera = I.carrera)
                        JOIN sga_sedes S ON (S.sede = A.sede)
			JOIN sga_personas P ON (P.nro_inscripcion = A.nro_inscripcion)
			JOIN vw_datos_censales_actuales D ON (D.unidad_academica = P.unidad_academica AND D.nro_inscripcion = P.nro_inscripcion)
			LEFT JOIN mug_localidades L1 ON (D.loc_proc = L1.localidad)
                        LEFT JOIN sga_coleg_sec COL ON (P.colegio_secundario = COL.colegio)
                        LEFT JOIN mug_localidades L2 ON (COL.localidad = L2.localidad)
			WHERE 	PL.anio_academico = $anio
				AND PL.periodo_lectivo = 'Virtual'
		ORDER BY 4";
            
            $inscriptos_online = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            $resultado_inscr_virtual = array();
            
            foreach ($inscriptos_online AS $inscr)
            {
                $legajo = $inscr['LEGAJO'];
                $carrera = $inscr['CARRERA'];
                
                $sql = "SELECT comision FROM sga_comisiones WHERE anio_academico = $anio and periodo_lectivo = 'Virtual'";
                $comsiones = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

                $c1 = $comsiones[0]['COMISION'];
                $c2 = $comsiones[1]['COMISION'];
                $c3 = $comsiones[2]['COMISION'];
                   
                $sql = "SELECT resultado, materia 
                            FROM sga_cursadas
                            WHERE carrera = $carrera
                                  AND legajo = '$legajo'
                                  AND comision IN ($c1, $c2, $c3)";
                $resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                
                foreach ($resultado as $r)
                {
                    switch ($r['MATERIA'])
                    {
                        case '001': $inscr['RESULTADO_MATEM'] = $r['RESULTADO'];
                                    break; 
                        case '002': $inscr['RESULTADO_IVU'] = $r['RESULTADO'];
                                    break; 
                        case '003': $inscr['RESULTADO_RESOL'] = $r['RESULTADO'];
                                    break; 
                    }
                }
                $resultado_inscr_virtual[] = $inscr;
    
            }
            return $resultado_inscr_virtual;
	}
}
