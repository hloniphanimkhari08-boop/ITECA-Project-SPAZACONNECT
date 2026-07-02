<div id="toast"
     class="fixed bottom-6 right-6 z-50 bg-black text-white px-5 py-3 text-sm font-medium rounded-lg opacity-0 pointer-events-none transition-all duration-300 translate-y-2">
    <span id="toast-msg"></span>
</div>

<script src="<?= BASE_URL ?>/assets/js/utils.js"></script>
<script>
    window.AppConfig = {
        baseUrl: '<?= BASE_URL ?>'
    };
</script>
<?php if (isset($extraJs)): foreach ($extraJs as $js): ?>
    <script src="<?= $js ?>"></script>
<?php endforeach; endif; ?>
</body>
</html>