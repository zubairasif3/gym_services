<div class="reaction-wrapper">
    <div class="reaction-container" x-data="{ showPicker: @js($showEmojiPicker) }">
        <!-- Section Title -->
        <h5 class="reaction-title mb-3">
            <i class="fas fa-heart me-2" style="color: #ff4757;"></i>
            How do you feel about this service?
        </h5>
        
        <!-- Emoji Selection Grid -->
        <div class="emoji-grid">
            @foreach(\App\Models\GigReaction::EMOJIS as $emoji)
                @php
                    $count = $reactionCounts[$emoji] ?? 0;
                    $isSelected = $userReaction === $emoji;
                @endphp
                <button 
                    wire:click="react('{{ $emoji }}')" 
                    class="emoji-card {{ $isSelected ? 'selected' : '' }}"
                    title="React with {{ $emoji }}">
                    <div class="emoji-icon">{{ $emoji }}</div>
                    @if($count > 0)
                        <div class="emoji-count">{{ $count }}</div>
                    @endif
                    @if($isSelected)
                        <div class="selected-badge">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    @endif
                </button>
            @endforeach
        </div>
        
        <!-- Total Reactions Summary -->
        @php
            $totalReactions = array_sum($reactionCounts);
        @endphp
        @if($totalReactions > 0)
            <div class="reactions-summary mt-4">
                <div class="summary-header">
                    <i class="fas fa-users me-2"></i>
                    <span class="fw-bold">{{ $totalReactions }}</span> 
                    <span class="text-muted">{{ $totalReactions === 1 ? 'person reacted' : 'people reacted' }}</span>
                </div>
            </div>
        @endif
    </div>
    
    <style>
        .reaction-wrapper {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 16px;
            padding: 20px 25px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.04);
            border: 1px solid #e9ecef;
        }
        
        .reaction-title {
            color: #2d3436;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .emoji-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .emoji-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 8px;
            }
        }
        
        @media (max-width: 576px) {
            .emoji-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 8px;
            }
        }
        
        .emoji-card {
            position: relative;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 8px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 75px;
            overflow: hidden;
        }
        
        .emoji-card:hover {
            transform: translateY(-5px) scale(1.05);
            border-color: #00b3f1;
            box-shadow: 0 8px 25px rgba(0, 179, 241, 0.2);
            background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 100%);
        }
        
        .emoji-card.selected {
            border-color: #00b3f1;
            background: linear-gradient(135deg, #e3f5ff 0%, #f0f9ff 100%);
            box-shadow: 0 5px 20px rgba(0, 179, 241, 0.3);
        }
        
        .emoji-card:active {
            transform: scale(0.95);
        }
        
        .emoji-icon {
            font-size: 2rem;
            line-height: 1;
            margin-bottom: 5px;
            animation: bounce 0.5s ease;
        }
        
        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .emoji-card:hover .emoji-icon {
            animation: wiggle 0.5s ease;
        }
        
        @keyframes wiggle {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-10deg); }
            75% { transform: rotate(10deg); }
        }
        
        .emoji-count {
            font-size: 0.75rem;
            font-weight: 700;
            color: #00b3f1;
            background: white;
            padding: 2px 8px;
            border-radius: 20px;
            margin-top: 3px;
            box-shadow: 0 2px 8px rgba(0, 179, 241, 0.2);
        }
        
        .selected-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            color: #00b3f1;
            font-size: 1rem;
            animation: scaleIn 0.3s ease;
        }
        
        @keyframes scaleIn {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .reactions-summary {
            background: white;
            border-radius: 10px;
            padding: 10px 15px;
            text-align: center;
            border: 1px solid #e9ecef;
        }
        
        .summary-header {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            gap: 5px;
        }
        
        .summary-header i {
            color: #00b3f1;
            font-size: 0.9rem;
        }
        
        .summary-header .fw-bold {
            color: #00b3f1;
            font-size: 1.05rem;
        }
        
        /* Loading state */
        [wire\:loading] .emoji-card {
            opacity: 0.6;
            pointer-events: none;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .reaction-wrapper {
                padding: 15px 18px;
            }
            
            .emoji-icon {
                font-size: 1.75rem;
            }
            
            .emoji-card {
                min-height: 70px;
                padding: 10px 6px;
            }
            
            .reaction-title {
                font-size: 0.9rem;
                margin-bottom: 0.8rem;
            }
        }
    </style>
</div>

