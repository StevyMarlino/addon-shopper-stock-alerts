<?php

declare(strict_types=1);

namespace Stevymarlino\AddonShopperStockAlerts\Livewire\Pages\SubscriptionStock;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Mckenziearts\Icons\Untitledui\Enums\Untitledui;
use Shopper\Livewire\Pages\AbstractPageComponent;
use Shopper\Traits\HandlesAuthorizationExceptions;
use Stevymarlino\AddonShopperStockAlerts\Models\StockSubscription;

class Index extends AbstractPageComponent implements HasActions, HasSchemas, HasTable
{
    use HandlesAuthorizationExceptions;
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(StockSubscription::query()->with(['product', 'customer'])->latest())
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('Product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.email')
                    ->label(__('Customer'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('Subscribed at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('notified_at')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state === null ? __('Pending') : __('Notified'))
                    ->color(fn ($state): string => $state === null ? 'warning' : 'success'),
            ])
            ->filters([
                TernaryFilter::make('notified_at')
                    ->label(__('Status'))
                    ->nullable()
                    ->trueLabel(__('Notified'))
                    ->falseLabel(__('Pending'))
                    ->default(false),
            ])
            ->recordActions([
                Action::make('delete')
                    ->label(__('Delete'))
                    ->icon(Untitledui::Trash03)
                    ->iconButton()
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (StockSubscription $record) => $record->delete()),
            ])
            ->persistFiltersInSession();
    }

    public function render(): View
    {
        return view('stock-alerts::pages.subscriptions-index')
            ->title(__('Stock alerts'));
    }
}
