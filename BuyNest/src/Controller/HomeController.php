
#[Route('/home', name: 'app_home')]
public function home(): Response
{
    return new Response('Bem-vindo!');
}
