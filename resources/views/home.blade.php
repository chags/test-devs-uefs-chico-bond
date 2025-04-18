
@extends('layouts.app')

@section('title', 'Página Inicial')
@section('description', 'Bem-vindo ao Projeto UEFS - Netra!')

@section('content')

	<!--Hero-->
	<div class="pt-24">

		<div class="container flex flex-col flex-wrap items-center px-3 mx-auto md:flex-row">
			<!--Left Col-->
			<div class="flex flex-col items-start justify-center w-full text-center md:w-2/5 md:text-left">
				<p class="w-full uppercase tracking-loose">Projeto UEFS - Netra!</p>
				<h1 class="my-4 text-5xl font-bold leading-tight">TESTE PRÁTICO<h1>
				<p class="mb-8 text-2xl leading-normal">Click no botão abaixo para acessar a API do teste!</p>
				<a  href="{{ url('api/documentation')}}"
					class="px-8 py-4 mx-auto my-6 font-bold text-gray-800 bg-white rounded-full shadow-lg lg:mx-0 hover:underline">
					Acesso API
				</a>
			</div>
			<!--Right Col-->
			<div class="w-full py-6 text-center md:w-3/5">
				<img class="z-50 w-full md:w-4/5" src="/image/hero.png">
			</div>

		</div>

	</div>


	<div class="relative -mt-12 lg:-mt-24">
		<svg viewBox="0 0 1428 174" version="1.1" xmlns="http://www.w3.org/2000/svg"
			xmlns:xlink="http://www.w3.org/1999/xlink">
			<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				<g transform="translate(-2.000000, 44.000000)" fill="#FFFFFF" fill-rule="nonzero">
					<path
						d="M0,0 C90.7283404,0.927527913 147.912752,27.187927 291.910178,59.9119003 C387.908462,81.7278826 543.605069,89.334785 759,82.7326078 C469.336065,156.254352 216.336065,153.6679 0,74.9732496"
						opacity="0.100000001"></path>
					<path
						d="M100,104.708498 C277.413333,72.2345949 426.147877,52.5246657 546.203633,45.5787101 C666.259389,38.6327546 810.524845,41.7979068 979,55.0741668 C931.069965,56.122511 810.303266,74.8455141 616.699903,111.243176 C423.096539,147.640838 250.863238,145.462612 100,104.708498 Z"
						opacity="0.100000001"></path>
					<path
						d="M1046,51.6521276 C1130.83045,29.328812 1279.08318,17.607883 1439,40.1656806 L1439,120 C1271.17211,77.9435312 1140.17211,55.1609071 1046,51.6521276 Z"
						id="Path-4" opacity="0.200000003"></path>
				</g>
				<g transform="translate(-4.000000, 76.000000)" fill="#FFFFFF" fill-rule="nonzero">
					<path
						d="M0.457,34.035 C57.086,53.198 98.208,65.809 123.822,71.865 C181.454,85.495 234.295,90.29 272.033,93.459 C311.355,96.759 396.635,95.801 461.025,91.663 C486.76,90.01 518.727,86.372 556.926,80.752 C595.747,74.596 622.372,70.008 636.799,66.991 C663.913,61.324 712.501,49.503 727.605,46.128 C780.47,34.317 818.839,22.532 856.324,15.904 C922.689,4.169 955.676,2.522 1011.185,0.432 C1060.705,1.477 1097.39,3.129 1121.236,5.387 C1161.703,9.219 1208.621,17.821 1235.4,22.304 C1285.855,30.748 1354.351,47.432 1440.886,72.354 L1441.191,104.352 L1.121,104.031 L0.457,34.035 Z">
					</path>
				</g>
			</g>
		</svg>
	</div>

	<section id="posts" class="py-8 bg-white border-b">
    <div class="container flex flex-wrap pt-4 pb-12 mx-auto">
        <h1 class="w-full my-2 text-5xl font-bold leading-tight text-center text-gray-800">Postagens</h1>
        <div class="w-full mb-4">
            <div class="w-64 h-1 py-0 mx-auto my-0 rounded-t opacity-25 gradient"></div>
        </div>

		@if(count($posts) > 0)
			@foreach($posts as $post)
				<div class="flex flex-col flex-grow flex-shrink w-full p-6 md:w-1/3">
					<div class="flex-1 overflow-hidden bg-white rounded-t rounded-b-none shadow">
						<a href="#" class="flex flex-wrap no-underline hover:no-underline">
							<!-- Exibe o título -->
							<div class="w-full px-6 text-xl font-bold text-gray-800">{{ $post->title }}</div>

							<!-- Exibe o conteúdo -->
							<p class="px-6 mb-5 text-base text-gray-800">
								{{ $post->content }}
							</p>
						</a>
					</div>
					<div class="flex-none p-6 mt-auto overflow-hidden bg-white rounded-t-none rounded-b shadow">
						<!-- Exibe o autor -->
						<div class="flex items-center justify-between mb-4">
							<span class="text-sm text-gray-500">Por: {{ $post->user->name ?? 'Usuário desconhecido' }}</span>
							<span class="text-sm text-gray-500">{{ $post->created_at->format('d/m/Y') }}</span>
						</div>

						<!-- Exibe as tags -->
						<div class="flex flex-wrap gap-2 mb-4">
							@if($post->tags->isNotEmpty())
								@foreach($post->tags as $tag)
									<span class="inline-block px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-200 rounded-full">
										{{ $tag->name }}
									</span>
								@endforeach
							@else
								<span class="text-sm text-gray-500">Sem tags</span>
							@endif
						</div>

						<div class="flex items-center justify-start">
							<button
								class="px-8 py-4 mx-auto my-6 font-bold text-white rounded-full shadow-lg lg:mx-0 hover:underline gradient">
								Ler Mais
							</button>
						</div>
					</div>
				</div>
			@endforeach
		@else
			<p class="text-center text-gray-500">Nenhum post disponível.</p>
		@endif


		</div>
	</section>


	<section class="py-8 bg-gray-100">

		<svg class="wave-top" viewBox="0 0 1439 147" version="1.1" xmlns="http://www.w3.org/2000/svg"
			xmlns:xlink="http://www.w3.org/1999/xlink">
			<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
				<g transform="translate(-1.000000, -14.000000)" fill-rule="nonzero">
					<g class="wave" fill="#f8fafc">
						<path
							d="M1440,84 C1383.555,64.3 1342.555,51.3 1317,45 C1259.5,30.824 1206.707,25.526 1169,22 C1129.711,18.326 1044.426,18.475 980,22 C954.25,23.409 922.25,26.742 884,32 C845.122,37.787 818.455,42.121 804,45 C776.833,50.41 728.136,61.77 713,65 C660.023,76.309 621.544,87.729 584,94 C517.525,105.104 484.525,106.438 429,108 C379.49,106.484 342.823,104.484 319,102 C278.571,97.783 231.737,88.736 205,84 C154.629,75.076 86.296,57.743 0,32 L0,0 L1440,0 L1440,84 Z">
						</path>
					</g>
					<g transform="translate(1.000000, 15.000000)" fill="#FFFFFF">
						<g
							transform="translate(719.500000, 68.500000) rotate(-180.000000) translate(-719.500000, -68.500000) ">
							<path
								d="M0,0 C90.7283404,0.927527913 147.912752,27.187927 291.910178,59.9119003 C387.908462,81.7278826 543.605069,89.334785 759,82.7326078 C469.336065,156.254352 216.336065,153.6679 0,74.9732496"
								opacity="0.100000001"></path>
							<path
								d="M100,104.708498 C277.413333,72.2345949 426.147877,52.5246657 546.203633,45.5787101 C666.259389,38.6327546 810.524845,41.7979068 979,55.0741668 C931.069965,56.122511 810.303266,74.8455141 616.699903,111.243176 C423.096539,147.640838 250.863238,145.462612 100,104.708498 Z"
								opacity="0.100000001"></path>
							<path
								d="M1046,51.6521276 C1130.83045,29.328812 1279.08318,17.607883 1439,40.1656806 L1439,120 C1271.17211,77.9435312 1140.17211,55.1609071 1046,51.6521276 Z"
								opacity="0.200000003"></path>
						</g>
					</g>
				</g>
			</g>
		</svg>
	</section>

	<section class="container py-6 mx-auto mb-12 text-center">

		<h1 class="w-full my-2 text-5xl font-bold leading-tight text-center text-white">Tags</h1>
		<div class="w-full mb-4">
			<div class="w-1/6 h-1 py-0 mx-auto my-0 bg-white rounded-t opacity-25"></div>
		</div>

		<!-- Lista de Tags -->
		<h3 class="my-4 text-3xl leading-tight">
			@if(isset($tags) && $tags->isNotEmpty())
				@foreach($tags as $tag)
					<span class="inline-block px-3 py-1 text-sm font-semibold text-blue-700 bg-blue-200 rounded-full mr-2">
						{{ $tag->name }}
					</span>
				@endforeach
			@else
				<span class="text-gray-400">Nenhuma tag disponível.</span>
			@endif
		</h3>

	</section>


@endsection('content')