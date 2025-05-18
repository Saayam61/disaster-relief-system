import 'package:app/models/news_feed.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:app/main_layout.dart';
import 'package:app/providers/newsfeed_provider.dart';

class NewsFeed extends StatefulWidget {
  const NewsFeed({super.key});

  @override
  State<NewsFeed> createState() => _NewsFeedState();
}

class _NewsFeedState extends State<NewsFeed> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() =>
      Provider.of<NewsFeedProvider>(context, listen: false).fetchPosts());
  }

  @override
  Widget build(BuildContext context) {
    final provider = Provider.of<NewsFeedProvider>(context);
    final posts = provider.posts;

    return MainLayout(
      child: Padding(
        padding: const EdgeInsets.only(top: 16, left: 16, right: 16),
        child: provider.isLoading
            ? const Center(child: CircularProgressIndicator())
            : ListView.builder(
                itemCount: posts.length + 1, // +1 for the title card
                itemBuilder: (context, index) {
                  if (index == 0) {
                    // 🔥 Title Card at the top
                    return Card(
                      color: Colors.blueGrey,
                      margin: const EdgeInsets.only(bottom: 16),
                      child: Padding(
                        padding: const EdgeInsets.all(8.0),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: const [
                            Icon(Icons.newspaper, color: Colors.white),
                            SizedBox(width: 10),
                            Text(
                              'News Feed',
                              style: TextStyle(color: Colors.white),
                            ),
                          ],
                        ),
                      ),
                    );
                  }

                  // 👇 Real posts start from index 1 now
                  final post = posts[index - 1];

                  return Card(
                    margin: const EdgeInsets.only(bottom: 16),
                    color: Colors.amber.shade50,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        ListTile(
                          leading: CircleAvatar(
                            backgroundImage: NetworkImage(
                              'https://ui-avatars.com/api/?name=${Uri.encodeComponent(post.centerName)}&color=FFFFFF&background=263749&format=png',
                            ),
                            radius: 20,
                          ),
                          title: Text(post.centerName),
                          subtitle: Text(post.centerAddress),
                          trailing: Text(
                            timeAgo(post.createdAt),
                            style: const TextStyle(color: Colors.blueGrey),
                          ),
                        ),
                        Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text("Title: ${post.title}", style: const TextStyle(fontWeight: FontWeight.bold)),
                              const SizedBox(height: 4),
                              Text("Content: ${post.content}", textAlign: TextAlign.left),
                              if (post.imageUrl != null && post.imageUrl!.isNotEmpty) ...[
                                const SizedBox(height: 8),
                                Image.network('http://localhost:8000/storage/${post.imageUrl!}'),
                              ]
                            ],
                          ),
                        ),
                      ],
                    ),
                  );
                },
              ),
      ),
    );
  }
}
