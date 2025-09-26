
document.querySelectorAll('a[href^="#"]').forEach(a=>{
  a.addEventListener('click', e=>{ const href=a.getAttribute('href'); if(href.length>1){ e.preventDefault(); document.querySelector(href).scrollIntoView({behavior:'smooth', block:'start'});} });
});
const lb=document.getElementById('lightbox'); const lbImg=lb?lb.querySelector('img'):null;
document.querySelectorAll('.gallery-grid a').forEach(a=>{
  a.addEventListener('click', e=>{ if(!lb||!lbImg) return; e.preventDefault(); lbImg.src=a.getAttribute('href'); lb.classList.add('active'); });
});
lb && lb.addEventListener('click', ()=> lb.classList.remove('active'));
document.getElementById('y') && (document.getElementById('y').textContent = new Date().getFullYear());
