<select  form="{{ $form }}" id="monthSelect" class="form-control form-control-sm" name="month">
    <option></option>
    {{-- Get months --}}
    <?php
        $months = array();
        for( $m=1; $m<=12; ++$m ) {
            $months[date('m', mktime(0, 0, 0, $m, 1))] = date('F', mktime(0, 0, 0, $m, 1));
        }
    ?>
    {{-- Iterate to make options --}}
    @foreach ($months as $key => $month)
    <option value="{{ $key }}" {{ isset($monthSelected) && $monthSelected == $key ? 'selected' : '' }}>{{ $month }}</option>
    @endforeach
</select>
