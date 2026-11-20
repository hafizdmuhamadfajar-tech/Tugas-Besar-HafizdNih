    </main>
    <footer class="foot">
      <span>SIPN &middot; SMK Sangkuriang 1 Cimahi</span>
      <span><?= date('d/m/Y') ?></span>
    </footer>
  </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script>
  // beri label kolom pada tiap sel tabel (dipakai tampilan HP)
  document.querySelectorAll('.table-responsive > .table').forEach(function (t) {
    var h = [].map.call(t.querySelectorAll('thead th'), function (x) { return x.textContent.trim(); });
    t.querySelectorAll('tbody tr').forEach(function (tr) {
      [].forEach.call(tr.children, function (td, i) { td.setAttribute('data-label', h[i] || ''); });
    });
  });
</script>
  </body>
</html>
