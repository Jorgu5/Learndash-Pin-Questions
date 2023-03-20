<?php

	class LDPQ_Handler {
		private static LDPQ_Handler $instance;

		public static function get_instance(): LDPQ_Handler
		{
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		private function __construct() {
			add_action( 'learndash-quiz-actual-content-after', array($this, 'ldpq_enqueue_scripts' ) );
			add_action( 'wp_ajax_ldpq_save_question', array($this, 'ldpq_save_question' ) );
		}

		public function ldpq_enqueue_scripts(): void
		{
			wp_enqueue_script( 'ldpq-ajax-js', LDPQ_PLUGIN_URL . 'assets/ldpq.js', array( 'jquery' ), LDPQ_PLUGIN_VERSION, true );
			wp_localize_script( 'ldpq-ajax-js', 'ldpq_ajax_object', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ldpq_save_question_nonce' )
			) );
		}

		public function ldpq_save_question(): void
		{
			// Verify the nonce.
			if ( ! wp_verify_nonce($_POST['nonce'], 'ldpq_save_question_nonce')) {
				wp_send_json_error([
					'error' => __('Invalid nonce.', 'ldpq')
				]);
			}

			// Get the quiz ID and question ID.
			$quiz_id     = absint($_POST['quiz_id']);
			$question_id = absint($_POST['question_id']);

			error_log($quiz_id);
			error_log($question_id);

			// Get the quiz object for the target quiz.
			$target_quiz = new WpProQuiz_Model_QuizMapper();
			$question = $target_quiz->getQuestion($quiz_id, $question_id);

			// print error log
			error_log(print_r($question, true));
			// Get the question object for the specified question.
			$question = $target_quiz->get_question_by_id($question_id);

			// Add the question to the new quiz.
			$new_quiz_id = 123; // Replace with the ID of the target quiz
			$new_quiz    = LD_Quiz::get_quiz($new_quiz_id);
			$new_quiz->add_question( $question );

			error_log('Success!!!!!');
		}

		public function ldpq_save_question_nonce(): string {
			return wp_create_nonce( 'ldpq_save_question_nonce' );
		}
	}

