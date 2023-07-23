// Example starter JavaScript for disabling form submissions if there are invalid fields
(() => {
  "use strict";

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  const forms = document.querySelectorAll(".needs-validation");

  // Loop over them and prevent submission
  Array.from(forms).forEach((form) => {
    form.addEventListener(
      "submit",
      (event) => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }

        form.classList.add("was-validated");
      },
      false
    );
  });

  const DynamicCheckboxes = {
    checkboxes: document.querySelectorAll(
      ".dntheme-list-table tbody input[type=checkbox]"
    ),
    selectAllTarget: document.querySelector(
      '.column-cb input[type="checkbox"]'
    ),

    init() {
      this.shiftToSelect();
      this.selectAll();
    },
    shiftToSelect() {
      const checkboxes = this.checkboxes;
      let lastChecked;
      function handleCheck(event) {
        let between = false;
        if (event.shiftKey) {
          checkboxes.forEach((checkbox) => {
            if (checkbox === this || checkbox === lastChecked)
              between = !between;
            if (between) checkbox.checked = true;
          });
        }
        lastChecked = this;
      }

      checkboxes.forEach((checkbox) => {
        checkbox.addEventListener("click", handleCheck, false);
      });
    },
    selectAll() {
      this.selectAllTarget.addEventListener(
        "click",
        handleSelectAll.bind(this),
        false
      );

      function handleSelectAll() {
        let checked = false;
        if (this.selectAllTarget.checked) checked = true;
        this.checkboxes.forEach((checkbox) => {
          checkbox.checked = checked;
        });
      }
    },
  };
  if ($(".dntheme-list-table").length) {
    DynamicCheckboxes.init();
  }
})();
