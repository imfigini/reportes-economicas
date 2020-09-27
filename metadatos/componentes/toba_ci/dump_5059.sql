------------------------------------------------------------
--[5059]--  Porcentaje de Avance - CI 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'Reportes', --proyecto
	'5059', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_ci', --clase
	'14', --punto_montaje
	'ci_porcentaje_de_avance', --subclase
	'porcentaje_de_avance/ci_porcentaje_de_avance.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Porcentaje de Avance - CI', --nombre
	NULL, --titulo
	'0', --colapsable
	NULL, --descripcion
	NULL, --fuente_datos_proyecto
	NULL, --fuente_datos
	NULL, --solicitud_registrar
	NULL, --solicitud_obj_obs_tipo
	NULL, --solicitud_obj_observacion
	NULL, --parametro_a
	NULL, --parametro_b
	NULL, --parametro_c
	NULL, --parametro_d
	NULL, --parametro_e
	NULL, --parametro_f
	NULL, --usuario
	'2015-11-03 08:47:45', --creacion
	'arriba'  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_eventos
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_eventos (proyecto, evento_id, objeto, identificador, etiqueta, maneja_datos, sobre_fila, confirmacion, estilo, imagen_recurso_origen, imagen, en_botonera, ayuda, orden, ci_predep, implicito, defecto, display_datos_cargados, grupo, accion, accion_imphtml_debug, accion_vinculo_carpeta, accion_vinculo_item, accion_vinculo_objeto, accion_vinculo_popup, accion_vinculo_popup_param, accion_vinculo_target, accion_vinculo_celda, accion_vinculo_servicio, es_seleccion_multiple, es_autovinculo) VALUES (
	'Reportes', --proyecto
	'4995', --evento_id
	'5059', --objeto
	'procesar', --identificador
	'Procesar', --etiqueta
	'0', --maneja_datos
	NULL, --sobre_fila
	NULL, --confirmacion
	NULL, --estilo
	'apex', --imagen_recurso_origen
	'reloj.png', --imagen
	'0', --en_botonera
	'Actualiza la información correspondiente al porcentaje de avance de cada alumno en cada carrera. Tenga en consideración que este proceso demora bastante.', --ayuda
	'1', --orden
	NULL, --ci_predep
	'0', --implicito
	'0', --defecto
	NULL, --display_datos_cargados
	NULL, --grupo
	NULL, --accion
	'0', --accion_imphtml_debug
	NULL, --accion_vinculo_carpeta
	NULL, --accion_vinculo_item
	NULL, --accion_vinculo_objeto
	'0', --accion_vinculo_popup
	NULL, --accion_vinculo_popup_param
	NULL, --accion_vinculo_target
	NULL, --accion_vinculo_celda
	NULL, --accion_vinculo_servicio
	'0', --es_seleccion_multiple
	'0'  --es_autovinculo
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_mt_me
------------------------------------------------------------
INSERT INTO apex_objeto_mt_me (objeto_mt_me_proyecto, objeto_mt_me, ev_procesar_etiq, ev_cancelar_etiq, ancho, alto, posicion_botonera, tipo_navegacion, botonera_barra_item, con_toc, incremental, debug_eventos, activacion_procesar, activacion_cancelar, ev_procesar, ev_cancelar, objetos, post_procesar, metodo_despachador, metodo_opciones) VALUES (
	'Reportes', --objeto_mt_me_proyecto
	'5059', --objeto_mt_me
	NULL, --ev_procesar_etiq
	NULL, --ev_cancelar_etiq
	'100%', --ancho
	'100%', --alto
	NULL, --posicion_botonera
	NULL, --tipo_navegacion
	'0', --botonera_barra_item
	'0', --con_toc
	NULL, --incremental
	NULL, --debug_eventos
	NULL, --activacion_procesar
	NULL, --activacion_cancelar
	NULL, --ev_procesar
	NULL, --ev_cancelar
	NULL, --objetos
	NULL, --post_procesar
	NULL, --metodo_despachador
	NULL  --metodo_opciones
);

------------------------------------------------------------
-- apex_objeto_dependencias
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'3659', --dep_id
	'5059', --objeto_consumidor
	'5055', --objeto_proveedor
	'cuadro', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'3660', --dep_id
	'5059', --objeto_consumidor
	'5056', --objeto_proveedor
	'datos', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'3661', --dep_id
	'5059', --objeto_consumidor
	'5057', --objeto_proveedor
	'filtro', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'3662', --dep_id
	'5059', --objeto_consumidor
	'5058', --objeto_proveedor
	'formulario', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_ci_pantalla
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto_ci_pantalla (objeto_ci_proyecto, objeto_ci, pantalla, identificador, orden, etiqueta, descripcion, tip, imagen_recurso_origen, imagen, objetos, eventos, subclase, subclase_archivo, template, template_impresion, punto_montaje) VALUES (
	'Reportes', --objeto_ci_proyecto
	'5059', --objeto_ci
	'2060', --pantalla
	'pant_seleccion', --identificador
	'1', --orden
	'Selección', --etiqueta
	'Permite obtener los alumnos de TUDAI que tienen el % de avance requerido o más de materias aprobadas según su plan de estudios. Considera alumnos Regulares y Activos. Detalla: legajo, nombre, carrera, plan, %, si sólo le falta la tesis, fecha de ingreso, fecha de última actividad académica (sea cursada o final, aprobado o no), fecha de PPS, cantidad de cursadas aprobadas totales, cantidad de cursadas optativas, cantidad total de finales aprobados y cantidad de finales de optativas aprobados. 
La actualización de la tabla que emite el reporte demora bastante...
La consulta si no se especifica un porcentaje o es muy bajo, demora bastante.', --descripcion
	NULL, --tip
	'apex', --imagen_recurso_origen
	NULL, --imagen
	NULL, --objetos
	NULL, --eventos
	NULL, --subclase
	NULL, --subclase_archivo
	NULL, --template
	NULL, --template_impresion
	'14'  --punto_montaje
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objetos_pantalla
------------------------------------------------------------
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'2060', --pantalla
	'5059', --objeto_ci
	'2', --orden
	'3659'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'2060', --pantalla
	'5059', --objeto_ci
	'1', --orden
	'3661'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'2060', --pantalla
	'5059', --objeto_ci
	'0', --orden
	'3662'  --dep_id
);
