<table class="company-header">
    <tr>
        @if($companyLogo)
        <td class="company-logo">
            <img src="{{ $companyLogo }}" alt="{{ $companyName }}">
        </td>
        @endif
        <td>
            <div class="company-name">{{ $companyName }}</div>
            @if($companyAddress)
            <div class="company-address">{{ $companyAddress }}</div>
            @endif
        </td>
    </tr>
</table>
