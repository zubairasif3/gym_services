<div>
    <form method="POST" action="{{ route('locale.switch') }}" id="locale-switch-form-team-selector">
        @csrf
        <input type="hidden" name="locale" id="locale-input-team-selector" value="{{ app()->getLocale() }}">
        <div class="switch-toggle">
            <input
                id="language-toggle"
                class="check-toggle check-toggle-round-flat"
                type="checkbox"
                {{ app()->getLocale() === 'it' ? 'checked' : '' }}
                onchange="document.getElementById('locale-input-team-selector').value = this.checked ? 'it' : 'en'; document.getElementById('locale-switch-form-team-selector').submit();"
            >
            <label for="language-toggle"></label>
            <span class="on">{{ __('language.english') }}</span>
            <span class="off">{{ __('language.italian') }}</span>
        </div>
    </form>
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

</div>
