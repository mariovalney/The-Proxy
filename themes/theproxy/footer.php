<?php

/* 
 * The Footer of theme "AvantDoc".
 * 
 * @package Default
 */
global $toast;
?>
        </div> <!-- DIV main roll -->
        <footer class="page-footer blue darken-4">
                <div class="footer-copyright">
                    <div class="container center">
                        <?php echo sprintf( __('%d &copy; THE PROXY - Desenvolvido por Mário Valney usando <a href="http://projetos.jangal.com.br/avant"><strong>Avant</strong></a> para a cadeira de Segurança e Auditoria Web da Faculdade Evolução.'), date('Y') ) ?>
                    </div>
                </div>
        </footer>

        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="<?php theme_file_url('js/materialize.min.js') ?>"></script>
        <script type="text/javascript" src="<?php theme_file_url('js/main.js') ?>"></script>
        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-55bc8cc5b3437a18" async="async"></script>

        <?php if (!empty($toast)) {?>
            <script>Materialize.toast('<?php echo $toast ?>', 4000);</script>
        <?php } ?>
    </body>
</html>
