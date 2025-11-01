<div>

    <div class="switch-toggle">
        <input id="language-toggle" class="check-toggle check-toggle-round-flat" type="checkbox">
        <label for="language-toggle"></label>
        <span class="on">English</span>
        <span class="off">Italian</span>
    </div>
    {{-- google translator --}}
    <style>
        .skiptranslate {
            display: none !important;
        }
        body {
            top: 0px !important;
        }
        .switch-toggle {
            position: relative;
            display: inline-block;
            margin: 0 5px;
        }

        .switch-toggle > span {
            position: absolute;
            top: 9px;
            pointer-events: none;
            font-family: 'Helvetica', Arial, sans-serif;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            text-shadow: 0 1px 0 rgba(0, 0, 0, .06);
            width: 50%;
            text-align: center;
        }

        .switch-toggle input.check-toggle-round-flat:checked ~ .off {
            color: #00b3f1;
        }

        .switch-toggle input.check-toggle-round-flat:checked ~ .on {
            color: #fff;
        }

        .switch-toggle > span.on {
            left: 0;
            padding-left: 2px;
            color: #00b3f1;
        }

        .switch-toggle > span.off {
            right: 0;
            padding-right: 4px;
            color: #fff;
        }

        .switch-toggle .check-toggle {
            position: absolute;
            margin-left: -9999px;
            visibility: hidden;
        }

        .switch-toggle .check-toggle + label {
            display: block;
            position: relative;
            cursor: pointer;
            outline: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .switch-toggle input.check-toggle-round-flat + label {
            padding: 2px;
            width: 144px;
            height: 35px;
            background-color: #00b3f1;
            -webkit-border-radius: 60px;
            -moz-border-radius: 60px;
            -ms-border-radius: 60px;
            -o-border-radius: 60px;
            border-radius: 60px;
        }

        .switch-toggle input.check-toggle-round-flat + label:before, input.check-toggle-round-flat + label:after {
            display: block;
            position: absolute;
            content: "";
        }

        .switch-toggle input.check-toggle-round-flat + label:after {
            top: 4px;
            left: 4px;
            bottom: 4px;
            width: 70px;
            background-color: #fff;
            -webkit-border-radius: 52px;
            -moz-border-radius: 52px;
            -ms-border-radius: 52px;
            -o-border-radius: 52px;
            border-radius: 52px;
            -webkit-transition: margin 0.2s;
            -moz-transition: margin 0.2s;
            -o-transition: margin 0.2s;
            transition: margin 0.2s;
        }

        .switch-toggle input.check-toggle-round-flat + label:after {
            top: 4px;
            left: 4px;
            bottom: 4px;
            width: 70px;
            background-color: #fff;
            -webkit-border-radius: 52px;
            -moz-border-radius: 52px;
            -ms-border-radius: 52px;
            -o-border-radius: 52px;
            border-radius: 52px;
            -webkit-transition: margin 0.2s;
            -moz-transition: margin 0.2s;
            -o-transition: margin 0.2s;
            transition: margin 0.2s;
        }

            /* .switch-toggle input.check-toggle-round-flat:checked + label {
            } */

        .switch-toggle input.check-toggle-round-flat:checked + label:after {
            margin-left: 65px;
        }
    </style>

    <!-- Hidden Google Translate Element -->
    <div id="google_translate_element" style="display: none;"></div>

    <script type="text/javascript">
        function googleTranslateElementInit() {
          new google.translate.TranslateElement({
            pageLanguage: 'en',
            includedLanguages: 'en,it',
            autoDisplay: false
          }, 'google_translate_element');
        }

        // Wait for DOM to load
        document.addEventListener('DOMContentLoaded', function () {
          const toggle = document.getElementById('language-toggle');

          // 🔁 Sync toggle switch with current language
          function syncToggleWithLanguage() {
            const currentLang = getCurrentGoogleLanguage();
            toggle.checked = (currentLang === 'it');
          }

          // 🧠 Detect language from Google Translate cookie
          function getCurrentGoogleLanguage() {
            const match = document.cookie.match(/googtrans=\/[a-z]{2}\/([a-z]{2})/);
            return match ? match[1] : 'en';
          }

          // 📌 Change language when toggle is flipped
          toggle.addEventListener('change', function () {
            const selectedLang = this.checked ? 'it' : 'en';
            const select = document.querySelector("select.goog-te-combo");
            if (select) {
              select.value = selectedLang;
              select.dispatchEvent(new Event("change"));
            }
          });

          // 🔄 Sync the toggle with the language after a short delay
          setTimeout(syncToggleWithLanguage, 500); // Small delay to ensure Translate loads
        });
    </script>

    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit">
    </script>
</div>
