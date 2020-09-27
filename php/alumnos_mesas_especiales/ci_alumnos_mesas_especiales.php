<?php
require_once ('MisConsultas.php');

class ci_alumnos_mesas_especiales extends toba_ci
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
                //ei_arbol($filtro);
                $carrera = $filtro['carrera'];
                $datos = self::get_alumnos_aptos_mesa_especial($carrera);
                $cuadro->set_datos($datos);
            } 
            else 
            {
                $cuadro->limpiar_columnas();
            }
	}

	//-------------------------------------------------------------------------------------

        static function get_alumnos_aptos_mesa_especial($carrera)
        {
            $db = MisConsultas::getConexion ();
            
            $sql = "SELECT P.legajo, P.alumno, P.carrera, P.nombre_carrera, P.plan, P.porcentaje, P.fecha_ingreso, P.fecha_ultima_actividad
                        FROM rep_porcentaje_carrera P
                            JOIN sga_alumnos A ON (A.legajo = P.legajo AND A.carrera = P.carrera)
                        WHERE porcentaje > 60
                            AND P.carrera = $carrera
                            AND A.regular = 'S' 
                            AND A.calidad = 'A'";
            
            $alumnos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); 

            $alumnos_aptos = array();
            foreach ($alumnos AS $alu)
            {
                $legajo = $alu['LEGAJO'];
                $sql = "EXECUTE PROCEDURE sp_plan_de_alumno('EXA', $carrera, '$legajo', TODAY)";
                $datos = $db->query($sql)->fetchAll(PDO::FETCH_NUM); 
                $plan = $datos[0][0];
                $version = $datos[0][1];
                
                if (self::is_apto_pedir_mesa($db, $legajo, $carrera, $plan, $version))
                {
                    $alumnos_aptos[] = $alu;
                }
            }
            return $alumnos_aptos;
        }
        
        static function is_apto_pedir_mesa($db, $legajo, $carrera, $plan, $version)
        {
            //Decarta "Trabajo Final", "Proyecto Final", "Trabajo Especial de Licenciatura Fisica"
            $sql = "SELECT COUNT(*) AS cant
                        FROM sga_atrib_mat_plan
                            WHERE carrera = $carrera
                            AND plan = '$plan'
                            AND version = '$version'
                            AND tipo_materia = 'N'
                            AND materia NOT IN ('0210', '0208', '0205', '0202', '0201') 
                            AND materia NOT IN (
                                            SELECT materia FROM sga_cursadas 
                                                    WHERE carrera = sga_atrib_mat_plan.carrera
                                                            AND legajo = '$legajo'
                                                            AND resultado IN ('A', 'P')
                                            UNION
                                            SELECT materia FROM vw_hist_academica 
                                                    WHERE carrera = sga_atrib_mat_plan.carrera
                                                            AND legajo = '$legajo'
                                                            AND resultado = 'A'
                                                )";
            $resultado = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC); 
            
            if ($resultado[0]['CANT'] == 0)
            {
                return true;
            }
            return false;
        }
}