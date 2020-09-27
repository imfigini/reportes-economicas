<?php
require_once 'MisConsultas.php';

class ci_informe_inscrip_x_instancia extends toba_ci
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
                    $datos = self::get_inscripciones_x_instancia($anio);
                    $cuadro->set_datos($datos);
		} 
		else 
                {
                    $cuadro->limpiar_columnas();
		}
        }

	//-----------------------------------------------------------------------------------
        
        static function get_inscripciones_x_instancia($anio)
        {
            $db = MisConsultas::getConexionMini();
            $datos = array();
            
            //Matemática
            $cant_curs = self::get_inscripciones_cursada($db, $anio, '001');
            $cant_exam = self::get_inscripciones_examen($db, $anio, '001'); 
            $total = array_merge($cant_curs, $cant_exam);
            $matem['MATERIA'] = 'Matemática';
            foreach($total AS $c)
            {
                $matem[strtoupper ("$c[PERIODO]")] = $c['CANT'];
            }
            
            //Resolución de Problemas
            $cant_curs = self::get_inscripciones_cursada($db, $anio, '003');
            $cant_exam = self::get_inscripciones_examen($db, $anio, '003'); 
            $total = array_merge($cant_curs, $cant_exam);
            $resolucion['MATERIA'] = 'Resolución de Problemas';
            foreach($total AS $c)
            {
                $resolucion[strtoupper ("$c[PERIODO]")] = $c['CANT'];
            }

            //IVU
            $cant_curs = self::get_inscripciones_cursada($db, $anio, '002');
            $cant_exam = self::get_inscripciones_examen($db, $anio, '002'); 
            $total = array_merge($cant_curs, $cant_exam);
            $ivu['MATERIA'] = 'IVU';
            foreach($total AS $c)
            {
                $ivu[strtoupper ("$c[PERIODO]")] = $c['CANT'];
            }            
            
            $resultado = array(0=>$matem, 1=>$resolucion, 2=>$ivu);
            //ei_arbol($resultado, '$resultado');
            return $resultado;
        }
        
        static function get_inscripciones_cursada ($db, $anio, $materia)
        {
            
            $sql = "SELECT PL.periodo_lectivo AS periodo, COUNT(*) AS cant
                        FROM sga_periodos_lect PL
                        JOIN sga_comisiones C ON (C.anio_academico = PL.anio_academico AND C.periodo_lectivo = PL.periodo_lectivo)
                        JOIN sga_insc_cursadas IC ON (IC.comision = C.comision)
                            WHERE PL.anio_academico = $anio
                    		AND C.materia = '$materia'
                    GROUP BY 1";
            $resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        }
        
        static function get_inscripciones_examen ($db, $anio, $materia)
        {
            $sql = "SELECT TE.nombre AS periodo, COUNT(*) AS cant
                        FROM sga_turnos_examen TE
                        JOIN sga_insc_examen IE ON (IE.anio_academico = TE.anio_academico AND IE.turno_examen = TE.turno_examen)
                        WHERE TE.anio_academico = $anio
                                AND IE.materia = '$materia'
                    GROUP BY 1";
            $resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        }

}
