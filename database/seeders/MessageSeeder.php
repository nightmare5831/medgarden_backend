<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Message;
use App\Models\Comment;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $joao = User::where('email', 'dollycookie0710@gmail.com')->first();
        $maria = User::where('email', 'devnight0710@gmail.com')->first();
        $ana = User::where('email', 'tom8jerry0913@gmail.com')->first();

        // Create message 1
        $message1 = Message::create([
            'user_id' => $joao->id,
            'content' => 'Qual é o peso médio dos anéis de ouro? Estou interessado em comprar um anel de ouro e gostaria de saber mais detalhes sobre peso, pureza e preços.',
            'favorite' => [$maria->email, 'carlos@example.com'],
            'good' => [$maria->email, $ana->email, 'pedro@example.com'],
            'bad' => [],
        ]);

        Comment::create([
            'user_id' => $maria->id,
            'message_id' => $message1->id,
            'content' => 'Geralmente varia entre 3-8 gramas dependendo do modelo!',
        ]);

        Comment::create([
            'user_id' => $joao->id,
            'message_id' => $message1->id,
            'content' => 'Obrigado pela informação!',
        ]);

        Comment::create([
            'user_id' => $ana->id,
            'message_id' => $message1->id,
            'content' => 'Temos anéis de 5g em promoção. Confira!',
        ]);

        // Create message 2
        $message2 = Message::create([
            'user_id' => $ana->id,
            'content' => 'Quais são as tendências de joias para este ano? Estou procurando por peças modernas e elegantes.',
            'favorite' => [],
            'good' => [$maria->email],
            'bad' => [],
        ]);

        Comment::create([
            'user_id' => $maria->id,
            'message_id' => $message2->id,
            'content' => 'Minimalismo e peças delicadas estão em alta!',
        ]);

        echo "Created messages and comments successfully!\n";
    }
}
