<?php
require_once 'MisConsultas.php';

class ci_listado_docentes_planta extends toba_ci
{
	protected $s__datos_filtro;
        protected $s__dni;


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
                $datos = $this->get_listado($this->s__datos_filtro);
            } 
            else 
            {
                $datos = $this->get_listado();
            }
            $resultado = $this->get_emails($datos);
            $cuadro->set_datos($resultado);
	}

	function evt__cuadro__seleccion($datos)
	{
            $this->s__dni = $datos['documento'];
            $this->set_pantalla('pant_edicion');
	}

	//---- Formulario -------------------------------------------------------------------

	function conf__formulario(toba_ei_formulario $form)
	{
            $datos = $this->get_detalle_docente($this->s__dni);
            $form->set_datos($datos);
	}

	function resetear()
	{
            $this->dep('datos')->resetear();
            $this->set_pantalla('pant_seleccion');
	}

	//---- EVENTOS CI -------------------------------------------------------------------

	function evt__volver()
	{
            $this->resetear();
	}

        //---- FUNCIONES ---------------------------------------------------------------------
        
        function get_listado($filtro=array())
	{
            $anio = date("Y");
            $sql = "SELECT  DISTINCT D.documento, 
                            D.legajo, 
                            V.apellido_nombres 
                        FROM vw_planta_funcional V
                        JOIN docentes D ON (D.id = V.docente_id) 
                        WHERE V.anio_academico_nombre = '$anio'
                        AND V.categoria NOT IN ('AY2')";

            if (isset($filtro['apellido_nombres'])) 
            {
                $sql .= " AND apellido_nombres ILIKE ".quote("%{$filtro['apellido_nombres']}%");
            }

            if (isset($filtro['dpto'])) 
            {
                $sql .= " AND V.departamento ILIKE ".quote($filtro['dpto']);
            }
            
            $sql .= " ORDER BY apellido_nombres ";

            return toba::db('Docentes')->consultar($sql);
	}
        
        function get_detalle_docente($dni)
        {
            $anio = date("Y");
            $sql = "SELECT  DISTINCT D.documento,
                        V.categoria, 
                        V.dedicacion, 
                        V.designacion,
                        V.departamento,
                        V.apellido_nombres 
                    FROM vw_planta_funcional V
                    JOIN docentes D ON (D.id = V.docente_id) 
                    WHERE V.anio_academico_nombre = '$anio' 
                        AND D.documento = '$dni' ";
            $datos = toba::db('Docentes')->consultar($sql);
            $resultado['apellido_nombres'] = $datos[0]['apellido_nombres'];
            $resultado['categoria'] = '';
            $resultado['dedicacion'] = '';
            $resultado['designacion'] = '';
            $resultado['dpto'] = '';
            foreach ($datos as $d)
            {
                $resultado['categoria'] .= $d['categoria'].', ';
                $resultado['dedicacion'] .= $d['dedicacion'].', ';
                $resultado['designacion'] .= $d['designacion'].', ';
                $resultado['dpto'] .= $d['departamento'].', ';
            }
            $resultado['categoria'] = mb_strimwidth($resultado['categoria'], 0, strlen($resultado['categoria'])-2);
            $resultado['dedicacion'] = mb_strimwidth($resultado['dedicacion'], 0, strlen($resultado['dedicacion'])-2);
            $resultado['designacion'] = mb_strimwidth($resultado['designacion'], 0, strlen($resultado['designacion'])-2);
            $resultado['dpto'] = mb_strimwidth($resultado['dpto'], 0, strlen($resultado['dpto'])-2);
            return $resultado;
        }
        
        function get_emails($datos)
        {
            $resultado = array();
            $db = MisConsultas::getConexion();
            foreach($datos as $dato)
            {
                $dni = $dato['documento'];
                $legajo = $dato['legajo'];
                $email = $this->get_mail_ifx($db, $dni);
                if (!isset($email))
                {
                    $email = $this->get_mail_toba($legajo);
                }
                $dato['e_mail'] = $email;
                $resultado[] = $dato;
            }
            return $resultado;
        }
        
        function get_mail_ifx($db, $dni)
        {
            $sql = "SELECT e_mail
                            FROM gda_anun_conf_pers A
                            JOIN sga_personas P ON (P.nro_inscripcion = A.nro_inscripcion)
                            WHERE P.nro_documento = '$dni'";
            $e_mail = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            if (!isset($e_mail[0]['E_MAIL']) || trim($e_mail[0]['E_MAIL']) == '')
            {
                $sql = "SELECT e_mail 
                            FROM vw_datos_censales_actuales V
                            JOIN sga_personas P ON (P.nro_inscripcion = V.nro_inscripcion)
                            WHERE P.nro_documento = '$dni'";
                $e_mail = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            }
            if (isset($e_mail[0]['E_MAIL']) && trim($e_mail[0]['E_MAIL']) <> '')
            {
                return $e_mail[0]['E_MAIL'];
            }
            return null;                
        }
        
        function get_mail_toba($legajo)
        {
            $usuario = 'd'.trim($legajo);
            $sql = "SELECT email FROM apex_usuario WHERE usuario = '$usuario'";

            $e_mail = toba::db('toba_2_6')->consultar($sql);
            //ei_arbol(array($usuario, $e_mail, $sql));
            if (isset($e_mail[0]['email']) && trim($e_mail[0]['email']) <> '')
            {
                return $e_mail[0]['email'];
            }
            return null;
        }        
}

?>