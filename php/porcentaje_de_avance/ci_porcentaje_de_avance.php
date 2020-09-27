<?php
require_once ('MisConsultas.php');

class ci_porcentaje_de_avance extends toba_ci
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

	static function get_alumnos_con_porcentaje($porcentaje)
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT P.*, A.fecha AS pps
                            FROM rep_porcentaje_carrera P
                            LEFT JOIN sga_activ_alumno A ON (A.carrera = P.carrera AND A.legajo = P.legajo)
                                WHERE P.carrera = 213 ";     //TUDAI
		if (trim($porcentaje) != "")
		{
			$sql = $sql . " AND porcentaje >= $porcentaje" ;
		}
		$sql = $sql . " ORDER BY alumno";
		//ei_arbol($sql);
		$alumnos = $db->query($sql)->fetchAll(); //PDO::FETCH_NUM
                $resultado = self::get_cantidad_curadas_finales($db, $alumnos);
		return $resultado;
	}

        static function get_cantidad_curadas_finales($db, $alumnos)
        {
            $datos = array();
            foreach($alumnos AS $a)
            {
                $legajo = $a['LEGAJO'];
                $carrera = $a['CARRERA'];
                $sql = "SELECT COUNT(materia) AS cant
                            FROM sga_cursadas 
                                WHERE legajo = '$legajo'
                                    AND carrera = $carrera
                                    AND resultado IN ('A', 'P')";
                $cursadas = $db->query($sql)->fetchAll(); //PDO::FETCH_NUM
                
                $sql = "SELECT COUNT(materia) AS cant 
                            FROM sga_cursadas 
                                WHERE carrera = $carrera
                                    AND legajo = '$legajo'
                                    AND resultado IN ('A', 'P')
                                    AND materia IN (
                                            SELECT materia 
                                                FROM sga_atrib_mat_plan 
                                                WHERE carrera = sga_cursadas.carrera 
                                                    AND tipo_materia = 'O'
                                        )";
                $cursadas_opt = $db->query($sql)->fetchAll(); //PDO::FETCH_NUM
                
                $sql = "SELECT COUNT(materia) AS cant
                            FROM vw_hist_academica 
                                WHERE legajo = '$legajo'
                                    AND carrera = $carrera
                                    AND resultado = 'A'";
                $finales = $db->query($sql)->fetchAll(); //PDO::FETCH_NUM
                
                $sql = "SELECT COUNT(materia) AS cant
                            FROM vw_hist_academica 
                                WHERE legajo = '$legajo'
                                    AND carrera = $carrera
                                    AND resultado = 'A'
                                    AND materia IN (
                                            SELECT materia 
                                                FROM sga_atrib_mat_plan 
                                                WHERE carrera = vw_hist_academica.carrera 
                                                    AND tipo_materia = 'O'
                                        )";

                $finales_opt = $db->query($sql)->fetchAll(); //PDO::FETCH_NUM


                $a['CANT_CURSADAS'] = $cursadas[0]['CANT'];
                $a['CANT_CURSADAS_OPT'] = $cursadas_opt[0]['CANT'];
                $a['CANT_FINALES'] = $finales[0]['CANT'];
                $a['CANT_FINALES_OPT'] = $finales_opt[0]['CANT'];
                $datos[] = $a;
            }
            return $datos;
        }
		
	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		if (isset($this->s__datos_filtro)) 
		{
			$filtro = $this->s__datos_filtro;
			//ei_arbol($filtro);
			$porcentaje = $filtro['porcentaje'];
			$datos = self::get_alumnos_con_porcentaje($porcentaje);
			$cuadro->set_datos($datos);
		} 
		else 
		{
			$cuadro->limpiar_columnas();
			//$cuadro->set_datos($this->dep('datos')->tabla('prueba')->get_listado());
		}
	}

	
	//-----------------------------------------------------------------------------------
	//---- Formulario ---------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__formulario__procesar()
	{
		$db = MisConsultas::getConexion();
               // ei_arbol('entro'); die;
		$sql = "EXECUTE PROCEDURE sp_rep_porcentaje_carrera()";
		$db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
	}

	function get_ultima_actualizacion()
	{
		$db = MisConsultas::getConexion();
		$sql = "SELECT MAX(fecha_ultima_actualizacion) AS fecha_ultima_actualizacion
					FROM rep_fecha_actualiz_tablas
						WHERE tabla = 'rep_porcentaje_carrera'";
		$fecha = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $fecha[0]['FECHA_ULTIMA_ACTUALIZACION'];		
	}

}