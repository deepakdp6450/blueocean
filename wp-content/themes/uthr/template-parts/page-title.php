<div class="uthr-page-header section"<?php if(!empty(get_option( 'uthr_page_title_bgimage' ))){ ?> data-bg-image="<?php echo esc_url( get_option( 'uthr_page_title_bgimage' )); ?>"<?php } if(!empty(get_option( 'uthr_page_title_bgcolor' ))){ ?>  data-bg-color="<?php echo esc_url( get_option( 'uthr_page_title_bgcolor' )); ?>" <?php } ?>>
    <div class="container">
        <div class="row">
            <!-- Page Header Content Start -->
            <div class="col-12">
                <div class="uthr-page-header-content">
                    <h1 class="title">
                        <?php
                            if( is_home() ){
                                echo esc_html__( 'Blog', 'uthr' );
                            }else{
                                if( is_singular( 'post' ) ){
                                    echo esc_html( 'Blog Details' );
                                }else{
                                    wp_title('');
                                }
                            }
                        ?>
                    </h1>
                    <?php
                        if( !is_home() ){
                            uthr_breadcrumbs();
                        }
                    ?>
                </div>
            </div>
            <!-- Page Header Content End -->
        </div>
    </div>
</div>