<?php
require_once('MisConsultas.php');
require_once('enviar_email.php');

class ci_notificar_falta_encuesta extends toba_ci
{
	protected $s__datos;
	
	function get_alumnos_notificar()
	{
		$db = MisConsultas::getConexion ();
		$sql = "SELECT DISTINCT sga_personas.apellido || ', ' || sga_personas.nombres AS alumno, e_mail
					FROM vw_datos_censales_actuales, sga_personas        
					WHERE 
							vw_datos_censales_actuales.nro_inscripcion = sga_personas.nro_inscripcion
						AND        
							vw_datos_censales_actuales.nro_inscripcion IN (
								   SELECT DISTINCT legajo
								   FROM sed_completar
								   WHERE relevamiento IN (12, 15)
							)";
		
		$alumnos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $alumnos;
	}
	
	//---- Cuadro -----------------------------------------------------------------------

	function conf__cuadro(toba_ei_cuadro $cuadro)
	{
		$datos = $this->get_alumnos_notificar();
		$this->s__datos = $datos;
		$cuadro->set_datos($datos);
	}

	function evt__cuadro__seleccion($datos)
	{
		$this->dep('datos')->cargar($datos);
	}

	

	//-----------------------------------------------------------------------------------
	//---- Eventos ----------------------------------------------------------------------
	//-----------------------------------------------------------------------------------

	function evt__enviar()
	{
		try
		{
			foreach ($this->s__datos AS $dato) 
			{
				if (isset($dato['E_MAIL']) AND $dato['E_MAIL'] <> '')
				{	
					//if (DESARROLLO)
					{
						$dato['E_MAIL'] = 'imfigini@exa.unicen.edu.ar';					//Esta línea sacarla en producción
						//$dato['E_MAIL'] = 'mariano.andres.martinez@gmail.com';			//Esta línea sacarla en producción
					}
					enviar_email::enviarMail('',$dato);
				}
				else
				{
					$nombre = $dato['NOMBRE'];
					//echo "<script>alert('Se intentó enviar un correo a $nombre pero no tiene e-mail establecido.')</script>"; 
					toba::notificacion()->agregar('Se intentó enviar un correo a '.$nombre.' pero no tiene e-mail establecido.');
				}				
			}
		}
		catch (Exception $e)
		{
			print_r($e->getMessage());
		}
	}

}
?>