<?php
/*
Plugin Name: My Plugin
Plugin URI: http://thg.com.br/portfolio
Description: Plugin de questionario.
Version: 1.0
Author: Thiago Santos
Author URI: http://www.thg.com.br/
Text Domain: my-plugin
Domain Path: /languages
*/

function cgc_ub_action_links($actions, $user_object) {
	$actions['edit_badges'] = "<a class='cgc_ub_edit_badges' href='" . admin_url( "users.php?page=cgc-badges&action=cgc_edit_badges&amp;user=$user_object->ID") . "'>" . __( 'Edit Badges', 'cgc_ub' ) . "</a>";
	return $actions;
}
add_filter('quiz-cons_row_actions', 'cgc_ub_action_links', 10, 2);

add_action('init', 'quiz_function');

function quiz_function(){
        $labels = array(
          'name'               => "Quiz Consultoria",
          'menu_name'          => "Quiz Consultorias",
          'name_admin_bar'     => "Quiz Consultoria",
          'add_new'            => "Cadastrar quiz",
          'add_new_item'       => "Novo Quiz",
          'edit_item'          => "Editar dados do quiz",
          'view_item'          => "Visualizar quiz",
          'search_items'       => "Procurar quiz",
          "singular_name"	   => 'Quiz Consultorias',
          'not_found'          => "Registro não encontrado",
           );

        $args = array( 
          'labels'             => $labels,
        	'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
          'rewrite'            => array( 'slug' => 'quiz-cons' ),
          	'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
          'menu_icon'   => 'dashicons-welcome-view-site',
          'has_archive' => true,
          'supports'           => array( 'title', 'editor', 'author', 'thumbnail'),
        );
    register_post_type( 'quiz-cons', $args );
	
	if(!function_exists('cmb_init')){
			if(!class_exists('CMB_Meta_Box')){
				include_once('includes/Custom-Meta-Boxes-master/custom-meta-boxes.php');				
			}
		}
	
	add_filter('cmb_meta_boxes', 'my_post_type_metadata' );
	
}


add_filter('manage_edit-produtos_columns', 'manage_edit_produtos_columns');

function manage_edit_produtos_columns( $columns ){
  $columns = array(
      'cb'    => '<input type="checkbox"/>',
      'title' => 'quiz-cons',
      'resumo' => 'Resumo',
      'cores' => 'Cores',
      'thumbnail' => 'Foto',
      //'perqunta' => 'Perquntas'
    );

  return $columns;
}

add_action('manage_produtos_posts_custom_column', '_manage_produtos_posts_custom_column');
    
function _manage_produtos_posts_custom_column( $column){
  global $post;
  switch ( $column ) {
    case 'cores':
    $cores = get_post_meta( $post->ID, 'cores', true );
    if( !empty( $cores ) ){
        echo $cores;
      }else{
        echo '-';
      }
    break;
    case 'resumo':
      $resumo = get_post( $post->ID );
      if ( !empty( $resumo->post_excerpt ) ) {
        echo $resumo->post_excerpt;
      }else{
        echo '-';
      }
    break;
    case 'thumbnail':
      if ( $thumb = get_the_post_thumbnail( $post->ID, 'thumbnail' ) ) {
          echo $thumb;
      }else{
        echo '-';
      }
    break;
  }
}

/**
 * suvermonkey site de pesquisa 
 * 
 * 
 * 
 * 
 * ***/

add_filter('meta_quiz', 'meta_quiz');

function remove_menu_items(){
	if( !current_user_can( 'administrator' ) )
	{
		remove_menu_page('edit.php?post_type=produtos');
		if( !empty( $_GET['post_type'] ) && $_GET['post_type'] == 'quiz-cons' )
		{
			$url = get_option('site_url') . '/canal/wp-admin/index.php';
			wp_redirect($url);
			exit;
		}
	}
}
add_action("admin_menu", 'remove_menu_items');


function my_post_type_metadata(array $meta_boxes){
$u_course_fields = array(	
			array( 'id' => 'qntper', 'name' => __('Quantas perguntas por página que você gostaria?','cactusthemes'), 'type' => 'text','desc' => __('Deixe 0 para todas as questões em uma página','cactusthemes') , 'repeatable' => false, 'multiple' => false),
			array( 'id' => 'qntuser', 'name' => __('Quantas vezes pode um usuário faça o teste?','cactusthemes'), 'type' => 'text','desc' => __('Deixe 0 para quantas vezes o usuário quiser. Atualmente só funciona para usuários registrados','cactusthemes') , 'repeatable' => false, 'multiple' => false),
			array( 'id' => 'contato', 'name' => __('Gostaria de pedir as informações de contato no início ou no fim do questionário?','cactusthemes'),'type' => 'select', 'options' => array( 'inicio' => 'Começando', 'requerid' => 'Obrigatorio', 'final' => 'Final'), 'desc' => __('Escolha uma opcao','cactusthemes'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'pediremail', 'name' => __('Devemos pedir-mail do usuário? ','cactusthemes'),'type' => 'select', 'options' => array( 'sim' => 'Sim','requerid' => 'Obrigatorio', 'nao' => 'Não'),'desc' => __('Escolha uma opcao','cactusthemes'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'pedircoment', 'name' => __('Gostaria de um lugar para o usuário inserir comentários?','cactusthemes'),'type' => 'select', 'options' => array( 'sim' => 'Sim', 'nao' => 'Não'),'desc' => __('Escolha uma opcao','cactusthemes'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'enviaremailuser', 'name' => __('Enviar e-mail do usuário após a conclusão? ','cactusthemes'),'type' => 'select', 'options' => array( 'sim' => 'Sim', 'nao' => 'Não'),'desc' => __('Escolha uma opcao','cactusthemes'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'enviaremailadmin', 'name' => __('Enviar e-mail de administrador após a conclusão?','cactusthemes'),'type' => 'select', 'options' => array( 'sim' => 'Sim', 'nao' => 'Não'),'desc' => __('Escolha uma opcao','cactusthemes'), 'repeatable' => false, 'multiple' => false ),
			
			
			
			array( 'id' => 'u-course-dur', 'name' =>  __('Duration:','cactusthemes'), 'type' => 'text','desc' => __('Course duration info','cactusthemes'), 'repeatable' => false, 'multiple' => false ),
			array( 'id' => 'course_member_id', 'name' => __('Speakers','cactusthemes') ,'desc' => __('Choose from members','cactusthemes'), 'type' => 'post_select', 'use_ajax' => true, 'query' => array( 'post_type' => 'u_member' ), 'multiple' => true, 'repeatable' => false),
			array( 'id' => 'u-course-cre', 'name' => __('Credit:','cactusthemes'), 'type' => 'text','desc' => __('Number of course credits','cactusthemes'), 'repeatable' => false, 'multiple' => false),
			array( 'id' => 'u-course-sub', 'name' => __('Subscribe URL:','cactusthemes'), 'type' => 'text' ,'desc' => __('Link to a subscribe form. If empty, button is invisible','cactusthemes'), 'repeatable' => false, 'multiple' => false),
			array( 'id' => 'u-course-label', 'name' => __('Subscribe Button Text:','cactusthemes'), 'type' => 'text' ,'desc' => __('Text that appears on the subscribe button.','cactusthemes'), 'repeatable' => false, 'multiple' => false),
			array( 'id' => 'u-course-dl', 'name' => __('Download Button URL','cactusthemes'), 'type' => 'text','desc' => __('Download URL for course documents. If empty, button is invisible','cactusthemes'), 'repeatable' => false, 'multiple' => false),
			array( 'id' => 'u-course-label-bro', 'name' => __('Download Button Text','cactusthemes'), 'type' => 'text' ,'desc' => __('Text that appears on download button','cactusthemes'), 'repeatable' => false, 'multiple' => false),
			array( 'id' => 'u-course-resumo', 'name' => __('Resumo para pagina de cursos','cactusthemes'), 'type' => 'textarea' ,'desc' => __('Resumos para aparecer na pagina de cursos','cactusthemes'), 'repeatable' => false, 'multiple' => false),
			array( 'id' => 'u-cour-callaction', 'name' => __('Call to Action Text','cactusthemes'), 'type' => 'textarea','desc' => __('Text that appears before Subscribe Button','cactusthemes'), 'repeatable' => false, 'multiple' => false),
		);
	$meta_boxes[] = array(
			'title' => __('Layout settings','cactusthemes'),
			'pages' => 'quiz-cons',
			'fields' => $u_course_fields,
			'priority' => 'high'
		);
	return $meta_boxes;
}


