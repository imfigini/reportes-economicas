------------------------------------------------------------
--[4002]--  Informe No Ingresantes - CI 
------------------------------------------------------------

------------------------------------------------------------
-- apex_objeto
------------------------------------------------------------

--- INICIO Grupo de desarrollo 0
INSERT INTO apex_objeto (proyecto, objeto, anterior, identificador, reflexivo, clase_proyecto, clase, punto_montaje, subclase, subclase_archivo, objeto_categoria_proyecto, objeto_categoria, nombre, titulo, colapsable, descripcion, fuente_datos_proyecto, fuente_datos, solicitud_registrar, solicitud_obj_obs_tipo, solicitud_obj_observacion, parametro_a, parametro_b, parametro_c, parametro_d, parametro_e, parametro_f, usuario, creacion, posicion_botonera) VALUES (
	'Reportes', --proyecto
	'4002', --objeto
	NULL, --anterior
	NULL, --identificador
	NULL, --reflexivo
	'toba', --clase_proyecto
	'toba_ci', --clase
	'14', --punto_montaje
	'ci_informe_no_ingresantes', --subclase
	'informe_no_ingresantes/ci_informe_no_ingresantes.php', --subclase_archivo
	NULL, --objeto_categoria_proyecto
	NULL, --objeto_categoria
	'Informe No Ingresantes - CI', --nombre
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
	'2016-05-30 11:12:41', --creacion
	'abajo'  --posicion_botonera
);
--- FIN Grupo de desarrollo 0

------------------------------------------------------------
-- apex_objeto_mt_me
------------------------------------------------------------
INSERT INTO apex_objeto_mt_me (objeto_mt_me_proyecto, objeto_mt_me, ev_procesar_etiq, ev_cancelar_etiq, ancho, alto, posicion_botonera, tipo_navegacion, botonera_barra_item, con_toc, incremental, debug_eventos, activacion_procesar, activacion_cancelar, ev_procesar, ev_cancelar, objetos, post_procesar, metodo_despachador, metodo_opciones) VALUES (
	'Reportes', --objeto_mt_me_proyecto
	'4002', --objeto_mt_me
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
	'2687', --dep_id
	'4002', --objeto_consumidor
	'4003', --objeto_proveedor
	'cuadro_no_ingres_carrera_col', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'2688', --dep_id
	'4002', --objeto_consumidor
	'4004', --objeto_proveedor
	'cuadro_no_ingres_carrera_loc', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'3482', --dep_id
	'4002', --objeto_consumidor
	'4878', --objeto_proveedor
	'cuadro_no_ingres_col', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'3483', --dep_id
	'4002', --objeto_consumidor
	'4879', --objeto_proveedor
	'cuadro_no_ingres_loc', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'2689', --dep_id
	'4002', --objeto_consumidor
	'4005', --objeto_proveedor
	'cuadro_no_ingresantes', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'2684', --dep_id
	'4002', --objeto_consumidor
	'3999', --objeto_proveedor
	'cuadro_no_ingresantes_carrera', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'2683', --dep_id
	'4002', --objeto_consumidor
	'3998', --objeto_proveedor
	'datos', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'2686', --dep_id
	'4002', --objeto_consumidor
	'4001', --objeto_proveedor
	'filtro', --identificador
	NULL, --parametros_a
	NULL, --parametros_b
	NULL, --parametros_c
	NULL, --inicializar
	NULL  --orden
);
INSERT INTO apex_objeto_dependencias (proyecto, dep_id, objeto_consumidor, objeto_proveedor, identificador, parametros_a, parametros_b, parametros_c, inicializar, orden) VALUES (
	'Reportes', --proyecto
	'2685', --dep_id
	'4002', --objeto_consumidor
	'4000', --objeto_proveedor
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
	'4002', --objeto_ci
	'1733', --pantalla
	'pant_edicion', --identificador
	'1', --orden
	'Pantalla', --etiqueta
	'Resumen acerca de la cantidad de NO ingresantes (que se presentaron en alguna instancia del curso de ingreso pero no aprobaron nuca). Total - Cantidad de intentos fallidos (hasta 2017) - Por carrera - Por carrera y localidad de colegio secundario que egresaron - Por localidad de colegio secundario que egresaron (sin importar carrera) - Por carrera y colegio de Tandil - Por colegio de Tandil (sin importar carrera). 
Este reporte consulta la base de mini guarani para extraer los datos.', --descripcion
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
	'1733', --pantalla
	'4002', --objeto_ci
	'3', --orden
	'2684'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'1733', --pantalla
	'4002', --objeto_ci
	'1', --orden
	'2685'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'1733', --pantalla
	'4002', --objeto_ci
	'0', --orden
	'2686'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'1733', --pantalla
	'4002', --objeto_ci
	'6', --orden
	'2687'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'1733', --pantalla
	'4002', --objeto_ci
	'4', --orden
	'2688'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'1733', --pantalla
	'4002', --objeto_ci
	'2', --orden
	'2689'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'1733', --pantalla
	'4002', --objeto_ci
	'7', --orden
	'3482'  --dep_id
);
INSERT INTO apex_objetos_pantalla (proyecto, pantalla, objeto_ci, orden, dep_id) VALUES (
	'Reportes', --proyecto
	'1733', --pantalla
	'4002', --objeto_ci
	'5', --orden
	'3483'  --dep_id
);
