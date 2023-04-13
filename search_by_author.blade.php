@extends('layouts.base')

@section('title', __('messages.page.users_publications.home_title'))

@section('stylesheets')
<link rel="stylesheet" href="{{ asset('/css/' . grstring() . '/publications.min.css?v=' . grstring()) }}">
@endsection

@section('javascripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/mark.js/8.11.1/jquery.mark.min.js"></script>
<script src="{{ asset('/js/' . grstring() . '/publications.min.js?v=' . grstring()) }}"></script>
@endsection

@section('content')
<section class="cft_content_users_publications">
  @if(count($publications2))
  <div class="ui centered grid">
   <div class="ui row">
    <h2 class="ui header center aligned cft_content_header">{{ app()->getLocale() == 'pl' ? 'Publikacje pracownika' : 'Employee publications'}} {{ $user->degree != '' ? $user->degree->name . ' ' : '' }}{{ $user->name }} {{ $user->surname }}</h2>
   </div>
   <div class="ui row">
    <h4 class="ui header center aligned">{{ count($publications2) }} {{ app()->getLocale() == 'pl' ? 'publikacji opublikowanych do' : 'publications published to' }} {{ $date2 }} {{ app()->getLocale() == 'pl' ? 'roku w ORCID' : 'in ORCID' }} {!! $user->orcid_id != '' ? ' ' . html_entity_decode(link_to("https://orcid.org/{$user->orcid_id}", '<i class="external alternate icon cft_content_users_publications__icon"></i>', ['title' => $user->orcid_id])) : '' !!}</h4>
   </div>

   <div class="sixteen wide mobile only twelve wide tablet only ten wide computer large screen widescreen column">
    <div class="ui basic segment cft_content_users_publications__search">
     <div class="ui icon input cft_content_users_publications__search--div">
      <input type="text" placeholder="{{ app()->getLocale() == 'pl' ? 'Szukaj w publikacjach' : 'Search in publications' }}" class="cft_content_users_publications__search--input" data-ptype="orcid">
      <i class="search icon"></i>
     </div>
    </div>
   </div>

   <div class="sixteen wide mobile only twelve wide tablet only ten wide computer large screen widescreen column">
    <div class="ui raised segment">
     @include('shared.publications2', ['all' => '0', 'type' => "orcid"])
    </div>
    <div class="ui raised segment">
     @include('shared.publications3', ['show_dates' => '1'])
    </div>
    <div class="ui raised segment">
     @include('shared.publications2', ['all' => '0', 'type' => "orcid"])
    </div>
   </div>
  </div>

  <br><br>
  @endif

  @if(count($publications))
  <div class="ui centered grid">
   <div class="ui row">
    <h2 class="ui header center aligned cft_content_header">{{ app()->getLocale() == 'pl' ? 'Publikacje pracownika' : 'Employee publications'}} {{ $user->degree != '' ? $user->degree->name . ' ' : '' }}{{ $user->name }} {{ $user->surname }}</h2>
   </div>
   <div class="ui row">
    <h4 class="ui header center aligned">{{ count($publications) }} {{ app()->getLocale() == 'pl' ? 'publikacji opublikowanych do' : 'publications published to' }} {{ $date }} {{ app()->getLocale() == 'pl' ? 'roku w PBN' : 'in PBN' }}{!! $user->pbn_id != '' ? ' ' . html_entity_decode(link_to("https://pbn.nauka.gov.pl/core/#/person/view/{$user->pbn_id}/current", '<i class="external alternate icon cft_content_users_publications__icon"></i>', ['title' => $user->pbn_id])) : '' !!}</h4>
   </div>

   <div class="sixteen wide mobile only twelve wide tablet only ten wide computer large screen widescreen column">
    <div class="ui basic segment cft_content_users_publications__search">
     <div class="ui icon input cft_content_users_publications__search--div">
      <input type="text" placeholder="{{ app()->getLocale() == 'pl' ? 'Szukaj w publikacjach' : 'Search in publications' }}" class="cft_content_users_publications__search--input" data-ptype="pbn">
      <i class="search icon"></i>
     </div>
    </div>
   </div>

   <div class="sixteen wide mobile only twelve wide tablet only ten wide computer large screen widescreen column">
    <div class="ui raised segment">
     @include('shared.publications2', ['all' => '0', 'type' => "pbn"])
    </div>
    <div class="ui raised segment">
     @include('shared.publications', ['show_dates' => '1'])
    </div>
    <div class="ui raised segment">
     @include('shared.publications2', ['all' => '0', 'type' => "pbn"])
    </div>
   </div>
  </div>
  @endif

  @include('shared.loader')
</section>
@endsection
