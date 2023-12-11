<?php

namespace App\Livewire;

use App\Livewire\Forms\ReceiptForm;
use App\Models\Category;
use Exception;
use HelgeSverre\Extractor\Engine;
use HelgeSverre\Extractor\Facades\Extractor;
use HelgeSverre\Extractor\Text\ImageContent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class NewReceipt extends Component
{
    use WithFileUploads;

    public ReceiptForm $form;

    /** @var \Illuminate\Http\UploadedFile $image */
    #[Validate('image|max:3000')] // 3MB
    public $image;

    public float $autofillCents;
    public ?bool $autofillSuccess;
    public float $autofillSeconds;

    public function autofill()
    {
        $this->authorize('use-ai');
        $this->autofillSuccess = null;
        $startTime = microtime(true);


        $categories = Category::all()
            ->map(fn($category) => "`$category->slug`: $category->keywords")
            ->join("; ");


        try {
            $data = Extractor::fields(
                ImageContent::raw(file_get_contents($this->image->temporaryUrl())),
                fields: [
                    "store" => " The name of the business or store the receipt is from. Correct it if it isn't properly spelled or capitalized.",
                    "amount" => " The grand total of the receipt without commas or currency symbols. If you are unsure, set this to an empty string; do not attempt to calculate it.",
                    "description" => " A general description of what was purchased.",
                    "category" => " Whichever category is most appropriate ($categories).",
                    "items" => [
                        "text" => "name of the item purchased",
                        "price" => "price of the item",
                        "qty" => "how many was purchased, defaults to 1 if not specified",
                    ],
                ],
                model: Engine::GPT_4_VISION,
                maxTokens: 1000,
                temperature: 0.2,
            );

            $this->form->store = $data['store'] ?? '';
            $this->form->amount = $data['amount'] ?? '';
            $this->form->description = $data['description'] ?? '';
            $this->form->items = $data['items'] ?? '';
            $this->form->category_id = Category::where('slug', $data['category'])?->first()?->id;

            $this->autofillSuccess = true;
        } catch (Exception $exception) {
            $this->autofillSuccess = false;
        }
    }

    public function store()
    {
        $this->form->image = $this->image->storePublicly('receipts');
        $this->form->validate();
        $this->form->store();
    }

    #[Title('Add Receipt — Payback')]
    #[Layout('components.layouts.default')]
    public function render()
    {
        return view('livewire.new-receipt', [
            'envelopes' => auth()->user()->envelopes,
            'categories' => Category::all()
        ]);
    }
}
