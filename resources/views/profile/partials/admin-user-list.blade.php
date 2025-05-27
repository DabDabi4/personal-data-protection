<div class="mt-8">
    <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Усі користувачі</h3>

    <table class="w-full text-sm text-left text-gray-700 dark:text-gray-200">
        <thead class="bg-gray-200 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Ім'я</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Роль</th>
                <th class="px-4 py-2">Дія</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr class="border-b border-gray-300 dark:border-gray-600">
                    <td class="px-4 py-2">{{ $user->id }}</td>
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2">{{ $user->email }}</td>
                    <td class="px-4 py-2">{{ $user->role }}</td>
                    <td class="px-4 py-2">
                        @if(auth()->id() !== $user->id)
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Ви впевнені, що хочете видалити цього користувача?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-500 hover:text-red-700">Видалити</button>
                            </form>
                        @else
                            <span class="text-gray-500">Ви</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
