@extends('layouts.app')

@section('title', 'Contact Us — KiddosHeaven')

@section('content')
	<div class="max-w-6xl mx-auto bg-white rounded-xl shadow p-8 mt-8">
		<h1 class="text-4xl font-extrabold mb-6 text-[var(--color-primary-dark)]">Contact Us</h1>
		<p class="text-lg text-gray-700 mb-6">
			Have a question, suggestion, or just want to say hello? Fill out the form below and our team will get back to you as
			soon as possible. You can also find us on social media or visit our location below!
		</p>
		<div class="flex flex-col md:flex-row gap-10">
			<form action="#" method="post" class="space-y-4 flex-1">
				<div>
					<label for="name" class="block font-semibold mb-1">Name</label>
					<input type="text" id="name" name="name" required class="w-full rounded border border-gray-300 px-3 py-2">
				</div>
				<div>
					<label for="email" class="block font-semibold mb-1">Email</label>
					<input type="email" id="email" name="email" required
						class="w-full rounded border border-gray-300 px-3 py-2">
				</div>
				<div>
					<label for="message" class="block font-semibold mb-1">Message</label>
					<textarea id="message" name="message" rows="4" required class="w-full rounded border border-gray-300 px-3 py-2"></textarea>
				</div>
				<button type="submit"
					class="bg-[var(--color-primary)] text-white font-bold px-6 py-2 rounded hover:bg-[var(--color-primary-dark)] transition">Send
					Message</button>
			</form>
			<div class="flex-1 flex flex-col gap-6">
				<div>
					<h2 class="text-xl font-bold mb-3 text-[var(--color-primary-dark)]">Our Location</h2>
					<div class="rounded-xl overflow-hidden shadow" style="height:300px">
						<iframe
							src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3303.917234073964!2d-118.2436846847826!3d34.05223448060916!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80c2c7b5e4b0b7b7%3A0x8e8f6e8e8e8e8e8e!2sLos%20Angeles%2C%20CA!5e0!3m2!1sen!2sus!4v1700000000000!5m2!1sen!2sus"
							width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"
							referrerpolicy="no-referrer-when-downgrade"></iframe>
					</div>
				</div>
				<div>
					<h2 class="text-xl font-bold mb-3 text-[var(--color-primary-dark)]">Connect with us</h2>
					<div class="flex flex-col gap-3 text-base">
						<div class="flex gap-4 flex-wrap">
							<a href="https://facebook.com/kiddosheaven" target="_blank" class="text-blue-600 hover:text-blue-800"
								title="Facebook">
								<svg fill="currentColor" viewBox="0 0 24 24" class="w-7 h-7 inline">
									<path
										d="M22.675 0h-21.35C.595 0 0 .592 0 1.326v21.348C0 23.408.595 24 1.325 24h11.495v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.24l-1.918.001c-1.504 0-1.797.715-1.797 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.406 24 24 23.408 24 22.674V1.326C24 .592 23.406 0 22.675 0" />
								</svg>
							</a>
							<a href="https://instagram.com/kiddosheaven" target="_blank" class="text-pink-500 hover:text-pink-700"
								title="Instagram">
								<svg fill="currentColor" viewBox="0 0 24 24" class="w-7 h-7 inline">
									<path
										d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.334 3.608 1.308.974.974 1.246 2.241 1.308 3.608.058 1.266.069 1.646.069 4.85s-.012 3.584-.07 4.85c-.062 1.366-.334 2.633-1.308 3.608-.974.974-2.241 1.246-3.608 1.308-1.266.058-1.646.069-4.85.069s-3.584-.012-4.85-.07c-1.366-.062-2.633-.334-3.608-1.308-.974-.974-1.246-2.241-1.308-3.608C2.175 15.647 2.163 15.267 2.163 12s.012-3.584.07-4.85c.062-1.366.334-2.633 1.308-3.608C4.515 2.497 5.782 2.225 7.148 2.163 8.414 2.105 8.794 2.163 12 2.163zm0-2.163C8.741 0 8.332.012 7.052.07 5.771.128 4.659.334 3.678 1.315c-.98.98-1.187 2.092-1.245 3.373C2.012 5.668 2 6.077 2 12c0 5.923.012 6.332.07 7.612.058 1.281.265 2.393 1.245 3.373.98.98 2.092 1.187 3.373 1.245C8.332 23.988 8.741 24 12 24s3.668-.012 4.948-.07c1.281-.058 2.393-.265 3.373-1.245.98-.98 1.187-2.092 1.245-3.373.058-1.28.07-1.689.07-7.612 0-5.923-.012-6.332-.07-7.612-.058-1.281-.265-2.393-1.245-3.373-.98-.98-1.187-2.092-1.245-3.373C15.668.012 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zm0 10.162a3.999 3.999 0 1 1 0-7.998 3.999 3.999 0 0 1 0 7.998zm7.2-11.162a1.44 1.44 0 1 0 0 2.88 1.44 1.44 0 0 0 0-2.88z" />
								</svg>
							</a>
							<a href="https://www.tiktok.com/@kiddosheaven" target="_blank" class="text-black hover:text-gray-700"
								title="TikTok">
								<svg fill="currentColor" viewBox="0 0 24 24" class="w-7 h-7 inline">
									<path
										d="M12.75 2v12.25a2.25 2.25 0 1 1-2.25-2.25h.25V9.5h-1.25a3.75 3.75 0 1 0 3.75 3.75V2h-1.5zM17.5 2v2.25a3.75 3.75 0 0 0 3.75 3.75H22V2h-4.5z" />
								</svg>
							</a>
							<a href="https://wa.me/1234567890" target="_blank" class="text-green-500 hover:text-green-700" title="WhatsApp">
								<svg fill="currentColor" viewBox="0 0 24 24" class="w-7 h-7 inline">
									<path
										d="M20.52 3.48A12 12 0 0 0 12 0C5.37 0 0 5.37 0 12c0 2.12.55 4.18 1.6 6.01L0 24l6.18-1.62A11.97 11.97 0 0 0 12 24c6.63 0 12-5.37 12-12 0-3.19-1.24-6.19-3.48-8.52zM12 22c-1.85 0-3.66-.5-5.22-1.44l-.37-.22-3.67.96.98-3.58-.24-.37A9.96 9.96 0 0 1 2 12c0-5.52 4.48-10 10-10s10 4.48 10 10-4.48 10-10 10zm5.2-7.6c-.28-.14-1.65-.81-1.9-.9-.25-.09-.43-.14-.61.14-.18.28-.7.9-.86 1.08-.16.18-.32.2-.6.07-.28-.14-1.18-.44-2.25-1.4-.83-.74-1.39-1.65-1.55-1.93-.16-.28-.02-.43.12-.57.13-.13.28-.34.42-.51.14-.17.18-.29.28-.48.09-.18.05-.36-.02-.5-.07-.14-.61-1.47-.84-2.01-.22-.53-.45-.46-.62-.47-.16-.01-.36-.01-.56-.01-.2 0-.52.07-.8.34-.28.28-1.06 1.04-1.06 2.54 0 1.5 1.09 2.95 1.24 3.16.15.21 2.15 3.29 5.22 4.48.73.31 1.3.5 1.75.64.74.24 1.41.21 1.94.13.59-.09 1.8-.74 2.06-1.46.25-.72.25-1.34.18-1.46-.07-.12-.25-.18-.53-.32z" />
								</svg>
							</a>
							<a href="mailto:hello@kiddosheaven.com" class="text-gray-600 hover:text-[var(--color-primary)]" title="Email">
								<svg fill="currentColor" viewBox="0 0 24 24" class="w-7 h-7 inline">
									<path
										d="M12 13.065l-8.533-6.4A1 1 0 0 1 4 5h16a1 1 0 0 1 .533 1.665l-8.533 6.4zm8.533 1.87l-7.2 5.4a1 1 0 0 1-1.2 0l-7.2-5.4A1 1 0 0 1 4 17h16a1 1 0 0 1 .533-2.065z" />
								</svg>
							</a>
							<a href="tel:+1234567890" class="text-blue-500 hover:text-blue-700" title="Phone">
								<svg fill="currentColor" viewBox="0 0 24 24" class="w-7 h-7 inline">
									<path
										d="M6.62 10.79a15.053 15.053 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.11-.21c1.21.49 2.53.76 3.88.76a1 1 0 0 1 1 1v3.5a1 1 0 0 1-1 1C10.07 22 2 13.93 2 4.5a1 1 0 0 1 1-1H6.5a1 1 0 0 1 1 1c0 1.35.27 2.67.76 3.88a1 1 0 0 1-.21 1.11l-2.2 2.2z" />
								</svg>
								<span class="ml-2 align-middle">+1 234 567 890</span>
							</a>
						</div>
						<div class="mt-2 text-gray-700 flex items-center gap-2">
							<svg fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6 text-gray-500">
								<path
									d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
							</svg>
							<span>123 Toy Street, Los Angeles, CA</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
