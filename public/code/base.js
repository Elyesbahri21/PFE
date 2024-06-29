document.addEventListener('click', function(e) {
    var dropdownToggle = document.querySelector('.dropdown-toggle');
    var dropdownMenu = document.querySelector('.dropdown-menu');
    
    if (e.target === dropdownToggle) {
      dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    } else if (!dropdownMenu.contains(e.target)) {
      dropdownMenu.style.display = 'none';
    }
  });
  