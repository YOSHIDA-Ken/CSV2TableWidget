<?php
class Widget_CSS2Table extends WP_Widget {
        /**
         * 初期化処理（ウィジェットの各種設定）を行います。
         */
        public function __construct() {
                // 情報用の設定値
                $widget_options = [
                        'classname'                     => 'widget-template',
                        'description'                   => 'テンプレートウィジェットの説明文です。',
                        'customize_selective_refresh'   => true,
                ];

                // 操作用の設定値
                $control_options = [
                        'width' => 400,
                        'height' => 350,
                ];

                // 親クラスのコンストラクタに値を設定
                parent::__construct(
                        'widget-template',
                        'テンプレートウィジェット',
                        $widget_options,
                        $control_options
                );
        }

        /**
         * 管理画面のウィジェット設定フォームを出力します。
         *
         * @param array $instance   現在のオプション値が渡される。
         */
        public function form( $instance ) {
                // デフォルトのオプション値
                $defaults = array(
                        'title' => '',
                        'filename'  => ''
                );

                // デフォルトのオプション値と現在のオプション値を結合
                $instance   = wp_parse_args( (array) $instance, $defaults );

                // タイトル値の無害化（サニタイズ）
                $title  = sanitize_text_field( $instance['title'] );
                $filename  = sanitize_text_field( $instance['filename'] );
?>
    <!-- 設定フォーム: タイトル -->
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">タイトル</label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>

    <!-- 設定フォーム: ファイル名 -->
    <p>
        <label for="<?php echo $this->get_field_id( 'filename' ); ?>">CSVファイル名</label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'filename' ); ?>" name="<?php echo $this->get_field_name( 'filename' ); ?>" type="text" value="<?php echo esc_attr( $filename ); ?>" />
    </p>
<?php
        }

        /**
         * ウィジェットオプションのデータ検証/無害化
         *
         * @param array $new_instance   新しいオプション値
         * @param array $old_instance   以前のオプション値
         *
         * @return array データ検証/無害化した値を返す
         */
        public function update( $new_instance, $old_instance ) {

                // 一時的に以前のオプションを別変数に退避
                $instance = $old_instance;

                // 無害化（サニタイズ）
                $instance['title']  = sanitize_text_field( $new_instance['title'] );
                $instance['filename']  = sanitize_text_field( $new_instance['filename'] );

                return $instance;
        }

        /**
         * ウィジェットの内容をWebページに出力します（HTML表示）
         *
         * @param array $args       register_sidebar()で設定したウィジェットの開始/終了タグ、タイトルの開始/終了タグなどが渡される。
         * @param array $instance   管理画面から入力した値が渡される。
         */
        public function widget( $args, $instance ) {

                $title = empty( $instance['title'] ) ? '' : $instance['title'];
                $filename = ! empty( $instance['filename'] ) ? $instance['filename'] : '';
                echo $args['before_widget']; // ウィジェット開始タグ（<div>など ）
                if ( ! empty( $title ) ) {
                        echo $args['before_title'] . $title . $args['after_title'];
                }

                echo '<div class="textwidget">';
                try {
                        if (($handle = @fopen($filename, "r")) === FALSE) {
                                throw new Exception('can not read CSV file.');
                        }

                        echo '<table class="table">';
                        echo '<tbody">';
                        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                                $numOfFields = count($data);
                                echo '<tr class="tr">';
                                foreach ($data as $col) {
                                        echo '<td class="td">'. $col, '</td>', PHP_EOL;
                                }
                                echo '</tr>';
                                $row++;
                        }
                        echo '</tbody>';
                        echo '</table>';
                }
                catch (Exception $e) {
                        echo '<span class="error">', $e->getMessage(), '</error>';
                }
                echo '</div>';
                echo $args['after_widget'];
        }
}