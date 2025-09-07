@if(isset($purchase))
    <div id="reviewModal" class="modal">
        <div class="modal__content">
            <h3 class="modal__title">取引が完了しました。</h3>
            <form action="{{ route('review.store', ['purchase' => $purchase->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="evaluatee_id" id="evaluatee_id" value="">
                <div class="review-form">
                    <div class="review-form__comment">今回の取引相手はどうでしたか？</div>
                    <div class="stars" id="star-rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <span class="star" data-value="{{ $i }}">&#9733;</span>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating" value="">

                    @error('rating')
                        <p class="error-message error-message__review">{{ $message }}</p>
                    @enderror
                    @error('evaluatee_id')
                        <p class="error-message error-message__review">{{ $message }}</p>
                    @enderror
                </div>
                <div class="modal__btn">
                    <button type="submit" class="modal__btn-submit">送信する</button>
                </div>
            </form>
        </div>
    </div>
@endif